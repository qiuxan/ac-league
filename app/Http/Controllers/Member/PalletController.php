<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Member;
use App\Pallet;
use App\Product;
use App\Code;
use App\Carton;
use App\Batch;
use App\Utils;

class PalletController
{
    public function index(){
        return view( 'member.pallets.index' );
    }

    public function getPalletForm() {
        return view( 'member.pallets.form' );
    }

    public function getPalletList() {
        return view( 'member.pallets.list' );
    }

    public function getPalletProductList() {
        return view( 'member.pallets.product-list' );
    }

    public function getPalletBatchList() {
        return view( 'member.pallets.batch-list' );
    }

    public function getPalletAvailableCodesList( Request $request ){
        return view( 'member.pallets.available-codes-list' );
    }

    public function getPalletAvailableCartonsList( Request $request ){
        return view( 'member.pallets.available-cartons-list' );
    }

    public function getPalletAvailableCodes( Request $request) {
        // check if pallet exists
        $pallet = Pallet::find($request->input('pallet_id'));
        if (!$pallet) {
            return json_encode([
                'pallet_id' => 0
            ]);
        }

        // check if member have access to pallet
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        if ($pallet->member_id != $member->id) {
            return json_encode([
                'pallet_id' => 0
            ]);
        }

        // query the pallet available codes
        $pageSize = $request->input('pageSize');  
        $filters = $request->input('filters');

        $whereRaw = "codes.disposition_id = 1 AND codes.carton_id = 0";
        $binding_array = [];

        if($filters['search']){
            $binding_array['search_string'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");

            $whereRaw .= " AND ( LOWER(codes.full_code) LIKE :search_string";
            $whereRaw .= " OR LOWER(batches.batch_code) LIKE :search_string1)";
        }
        
        if($pallet->batch_id != 0){
            $batch_id = $pallet->batch_id;
            $whereRaw .= " AND codes.batch_id = {$batch_id}";
        }

        $available_codes = DB::table('codes')
            ->select('codes.*', 'batches.batch_code')
            ->join('batches', function($join) use ($pallet) {
                $join->on('codes.batch_id','=','batches.id')
                ->whereRaw("batches.product_id = {$pallet->product_id} AND batches.deleted = 0 AND batches.disposition_id = 1");
            })
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('id', 'desc')
            ->paginate( $pageSize );

        return response()->json($available_codes);
    }

    public function getPalletAvailableCartons( Request $request) {
        // check if pallet exists
        $pallet = Pallet::find( $request->input('pallet_id') );
        if (!$pallet) {
            return json_encode(
                ['pallet_id'=>0]
            );
        }

        // check if member has access to pallet
        $member = Member::where(['members.user_id'=>auth()->user()->id, 'members.deleted'=>0])->first();
        if ( $pallet->member_id != $member->id) {
            return json_encode(
                ['pallet_id'=>0]
            );
        }

        // query the available cartons
        $pageSize = $request->input('pageSize');
        $filters = $request->input('filters');
        $whereRaw = "cartons.product_id = {$pallet->product_id} AND cartons.member_id = {$member->id} AND cartons.pallet_id = 0 AND cartons.deleted = 0";
        if ($pallet->batch_id != 0) {
            $whereRaw .= " AND cartons.batch_id = {$pallet->batch_id}";
        }   
        $binding_array = [];
        if ($filters['search']) {
            $binding_array['search_string'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");

            $whereRaw .= " AND ( LOWER(cartons.sscc2_sn) LIKE :search_string";
            $whereRaw .= " OR LOWER(batches.batch_code) LIKE :search_string1 )";
        }

        $available_cartons = DB::table('cartons')
            ->select('cartons.*', 'products.name_en', 'batches.batch_code')
            ->leftJoin('products', 'cartons.product_id', '=', 'products.id')
            ->leftJoin('batches', 'batches.id', '=', 'cartons.batch_id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('id','desc')
            ->paginate( $pageSize );
        
        return response()->json($available_cartons);
    }

    public function getPallet(Request $request) {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();        
        
        $pallet = DB::table('pallets')
            ->select('pallets.*', 'batches.batch_code', 'products.name_en AS product_name')
            ->leftjoin('production_partners','pallets.production_partner_id','=', 'production_partners.id')
            ->leftjoin('batches', 'pallets.batch_id', '=', 'batches.id')
            ->leftjoin('products', 'pallets.product_id', '=', 'products.id')
            ->where([
                'pallets.deleted' => 0,
                'pallets.id' => $request->input('id'),
                'pallets.member_id' => $member->id
            ])
            ->first();
        
        return response()->json($pallet);
    }

    public function getPallets(Request $request) {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();        
        $pageSize = $request->input('pageSize');

        $whereRaw = " pallets.deleted = 0 AND pallets.member_id = {$member->id}";
        $binding_array = array();
        $filters = $request->input('filters');
        if ($filters['search']) {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(pallets.sscc3_sn) LIKE :search_string1";
            $whereRaw .= " OR LOWER(products.name_en) LIKE :search_string2)";
        }
        $pallets = DB::table('pallets')
            ->select('pallets.*', 'products.name_en')
            ->leftjoin('products', 'pallets.product_id', '=', 'products.id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('id', 'desc')
            ->paginate( $pageSize );
        
        return response()->json($pallets);        
    }

    public function store( Request $request ) {
        try {
            DB::beginTransaction();

            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if(!$request->input('batch_id')){
                $batch_id = 0;
            } else {
                $batch_id = $request->input('batch_id');
            }  

            $product = Product::find($request->input('product_id'));
            if ($product->member_id != $member->id) {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'pallet_id' => 0
                ]);                
            }

            $pallet_id = $request->input('id');
            $pallet = Pallet::find($pallet_id);
            if(!$pallet)
            {
                // check if sscc3_sn is taken
                $checked = Pallet::where([
                    'sscc3_sn' => $request->input('sscc3_sn'),
                    'deleted' => 0
                ])->first();

                if ($checked) {
                    return json_encode([
                        'pallet_id' => 0,
                        'error' => 1,
                        'error_msg' => 'SSCC3 SN is taken, please choose another one!'
                    ]);                    
                }

                $pallet = new Pallet();
                $pallet->created_by = auth()->user()->id;
            }
            else
            {
                // check if sscc3_sn is taken  
                $checked = Pallet::where([
                    'sscc3_sn'=>$request->input('sscc3_sn'),
                    'deleted'=>0
                ])->first();

                if(
                    $checked && 
                    ($checked->id != $pallet->id)
                ){
                    return json_encode([
                        'pallet_id' => 0,
                        'error' => 1,
                        'error_msg' => 'SSCC3 SN is taken, please choose another one!'
                    ]);                                        
                }                
                
                if( $pallet->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'pallet_id' => 0
                        ]);
                }

                // if product or batch is changed, then update the codes and cartons accordingly
                if ($pallet->product_id != $product->id) {
                    // clear carton pallet_id
                    DB::table('cartons')
                        ->where('cartons.pallet_id','=',$pallet->id)
                        ->update(['cartons.pallet_id'=>0]);

                    // clear code pallet_id
                    // pallet codes
                    DB::table('codes')
                        ->where(['codes.pallet_id'=>$pallet->id, 'codes.carton_id'=>-1])
                        ->update(['codes.carton_id'=>0, 'codes.pallet_id'=>0]);
                    // carton code
                    DB::table('codes')
                        ->where('codes.pallet_id','=',$pallet->id)
                        ->update(['codes.pallet_id'=>0]);                    
                } else if ($pallet->batch_id != $batch_id && $batch_id != 0) {
                    // clear carton codes
                    DB::table('codes')
                        ->join('cartons', 'cartons.id', '=', 'codes.carton_id')
                        ->whereRaw("codes.pallet_id = {$pallet->id} AND cartons.pallet_id = {$pallet_id} AND cartons.batch_id != {$batch_id}")
                        ->update(['codes.pallet_id'=>0]); 

                    // clear those carton not have same batch
                    DB::table('cartons')
                        ->whereRaw("cartons.pallet_id = {$pallet->id} AND cartons.batch_id != {$batch_id}")
                        ->update(['cartons.pallet_id'=>0]);

                    // clear those codes not have same batch
                    // pallet codes
                    DB::table('codes')
                        ->whereRaw("codes.pallet_id = {$pallet->id} AND codes.carton_id = -1 AND codes.batch_id != {$batch_id}")
                        ->update(['codes.carton_id'=>0,'codes.pallet_id'=>0]);
                }
            }

            // save the pallet
            $pallet->fill($request->all());
            // TODO: production partner
            $pallet->production_partner_id = 1;
            $pallet->member_id = $member->id;       
            $pallet->batch_id = $batch_id;   
            $pallet->product_id = $product->id;
            $pallet->save();

            DB::commit();

            return json_encode([
                'pallet_id' => $pallet->id
            ]);
        }
        catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'pallet_id' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deletePallets( Request $request ) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $pallet = Pallet::find($id);
                if($pallet->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($pallet)
                {
                    // check if there is cartons or items assigned to this pallet
                    // if there is carton assigned, set those cartons pallet_id to 0
                    // if there is item assigned, set those items carton_id and pallet_id to 0
                    DB::table('cartons')
                        ->where(['cartons.pallet_id' => $pallet->id])
                        ->update(['cartons.pallet_id' => 0]);

                    DB::table('codes')
                        ->where(['codes.pallet_id' => $pallet->id, 'codes.carton_id' => -1])
                        ->update(['codes.carton_id' => 0]);
                    
                    DB::table('codes')
                        ->where(['codes.pallet_id' => $pallet->id])
                        ->update(['codes.pallet_id' => 0]);

                    $pallet->deleted = 1;              
                    $pallet->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function getPalletItems( Request $request) {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();  
        $pallet_id = $request->input('pallet_id');
        $pallet = Pallet::find($pallet_id);
        if(!$pallet){
            return json_encode([
                'success' => false,
                'error' => 'pallet not found'
            ]);             
        }      
        if($member->id != $pallet->member_id){
            return json_encode([
                'success' => false,
                'error' => 'member has no access to pallet'
            ]);             
        }
        $pageSize = $request->input('pageSize');

        $whereRaw = " codes.pallet_id = {$pallet_id} ";
        $binding_array = array();        
        $filters = $request->input('filters');

        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string3'] = strtolower("%{$filters['search']}%");

            $whereRaw .= " AND (LOWER(codes.full_code) LIKE :search_string1 ";
            $whereRaw .= " OR LOWER(batches.batch_code) LIKE :search_string2 ";
            $whereRaw .= " OR LOWER(cartons.sscc2_sn) LIKE :search_string3 )";
        }

        $pallet_items = DB::table('codes')
            ->select('codes.full_code', 'codes.carton_id', 'cartons.sscc2_sn', 'codes.id', 'batches.batch_code')
            ->leftJoin('cartons', 'cartons.id', '=', 'codes.carton_id')
            ->leftJoin('batches', 'batches.id', '=', 'codes.batch_id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('codes.id','desc')
            ->paginate( $pageSize );

        return response()->json($pallet_items);        
    }

    public function savePalletItem( Request $request) {
        try {
            $member = Member::where(['members.user_id'=>auth()->user()->id,'deleted'=>0])->first();
            $ids = $request->input('ids');
            $pallet_id = $request->input('pallet_id');
            DB::beginTransaction();
            // check if member has access to pallet
            $pallet = Pallet::find($pallet_id);
            if(!$pallet){
                return json_encode([
                    'success' => false,
                    'error' => 'pallet not found'
                ]);                
            }

            if($pallet->member_id != $member->id){
                return json_encode([
                    'success' => false,
                    'error' => 'member has no access to pallet'
                ]);                                
            }
            foreach($ids as $id){
                $code = Code::find($id);
                if(!$code){
                    return json_encode([
                        'success' => false,
                        'error' => 'code not found'
                    ]);
                }
                $batch = Batch::find($code->batch_id);
                if(!$batch){
                    return json_encode([
                        'success' => false,
                        'error' => 'code batch not found'
                    ]);
                }
                // check if member has access to the codes                
                if($batch->member_id != $member->id) {
                    return json_encode([
                        'success' => false,
                        'error' => 'member no access to code batch'
                    ]);
                }
                // check if the code belongs to the right batch
                if ( ($pallet->batch_id !=0) && ($pallet->batch_id != $code->batch_id) ) {
                    return json_encode([
                        'success'   => false,
                        'error'     => 'code batch not match pallet batch'
                    ]);
                }
            }

            // update code pallet_id
            DB::table('codes')
                ->whereIn('codes.id',$ids)
                ->update(['codes.pallet_id'=>$pallet_id, 'codes.carton_id'=>-1]);

            DB::commit();            
            return json_encode([
                'success' => true
            ]);            
        } catch (Exception $e) {
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);       
        }        
    }        

    public function deletePalletItems( Request $request) {
        try{
            $member = Member::where(['members.user_id'=>auth()->user()->id,'deleted'=>0])->first();
            $ids = $request->input('ids');
            $pallet_id = $request->input('pallet_id');
            DB::beginTransaction();
            // check if member has access to pallet
            $pallet = Pallet::find($pallet_id);
            if(!$pallet){
                return json_encode([
                    'success' => false,
                    'error' => 'pallet not found'
                ]);
            }
            if($pallet->member_id != $member->id){
                return json_encode([
                    'success' => false,
                    'error' => 'member has no access to pallet'
                ]);                                
            }

            foreach($ids as $id){
                $code = Code::find($id);
                if(!$code){
                    return json_encode([
                        'success' => false,
                        'error' => 'code not found'
                    ]);
                }
                $batch = Batch::find($code->batch_id);
                if(!$batch){
                    return json_encode([
                        'success' => false,
                        'error' => 'code batch not found'
                    ]);
                }
                // check if each code is assigned to carton
                if($code->carton_id != -1){
                    return json_encode([
                        'success' => false,
                        'error' => 'code is assigned to carton'
                    ]);
                }              
                // check if member has access to the codes                  
                if($batch->member_id != $member->id) {
                    return json_encode([
                        'success' => false,
                        'error' => 'member no access to code batch'
                    ]);
                }
            }
            
            // update code pallet_id
            DB::table('codes')
                ->whereIn('codes.id',$ids)
                ->update(['codes.pallet_id'=>0, 'codes.carton_id'=>0]);

            DB::commit();            
            return json_encode([
                'success' => true
            ]);     
        }
        catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0,
                'msg' => $e->getMessage()
            ]);            
        }        
    }

    public function getPalletCartons( Request $request) {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();  
        $pallet_id = $request->input('pallet_id');
        $pallet = Pallet::find($pallet_id);
        if(!$pallet){
            return json_encode([
                'success' => false,
                'error' => 'pallet not found'
            ]);             
        }      
        if($member->id != $pallet->member_id){
            return json_encode([
                'success' => false,
                'error' => 'member has no access to pallet'
            ]);             
        }
        $pageSize = $request->input('pageSize');

        $whereRaw = " cartons.pallet_id = {$pallet_id} AND cartons.deleted = 0";
        $binding_array = array();        
        $filters = $request->input('filters');

        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");

            $whereRaw .= " AND (LOWER(cartons.sscc2_sn) LIKE :search_string1 ";
            $whereRaw .= "      OR LOWER(products.name_en) LIKE :search_string2 )";
        }

        $pallet_cartons = DB::table('cartons')
            ->select('cartons.*', 'products.name_en', 'batches.batch_code')
            ->leftJoin('products', 'cartons.product_id', '=', 'products.id')
            ->leftJoin('batches', 'batches.id', '=', 'cartons.batch_id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('cartons.id','desc')
            ->paginate( $pageSize );
            
        return response()->json($pallet_cartons);                
    }

    public function savePalletCarton( Request $request) {
        try {
            $member = Member::where(['members.user_id'=>auth()->user()->id,'deleted'=>0])->first();
            $ids = $request->input('ids');
            $pallet_id = $request->input('pallet_id');
            DB::beginTransaction();
            // check if member has access to pallet
            $pallet = Pallet::find($pallet_id);
            if(!$pallet){
                return json_encode([
                    'success' => false,
                    'error' => 'pallet not found'
                ]);                
            }
            if($pallet->member_id != $member->id){
                return json_encode([
                    'success' => false,
                    'error' => 'member has no access to pallet'
                ]);                                
            }
            foreach($ids as $id){
                $carton = Carton::find($id);
                if(!$carton){
                    return json_encode([
                        'success' => false,
                        'error' => 'carton not found'
                    ]);
                }
                // check if member has access to cartons                
                if($carton->member_id != $member->id) {
                    return json_encode([
                        'success' => false,
                        'error' => 'member has no access to carton'
                    ]);
                }
                // check if the carton and pallet have save batch
                if( ($pallet->batch_id != 0) && ($pallet->batch_id != $carton->batch_id) ){
                    return json_encode([
                        'success' => false,
                        'error' => 'carton batch not match pallet batch'
                    ]);                    
                }
            }
            // update pallet_id for each carton
            DB::table('cartons')
                ->whereIn('cartons.id',$ids)
                ->update(['cartons.pallet_id'=>$pallet_id]);
            // update carton item code pallet_id
            DB::table('codes')
                ->whereIn('codes.carton_id', $ids)
                ->update(['codes.pallet_id'=>$pallet_id]);

            DB::commit();            
            return json_encode([
                'success' => true
            ]);            
        } catch (Exception $e) {
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);       
        }          
    }

    public function deletePalletCartons( Request $request) {
        try{
            $member = Member::where(['members.user_id'=>auth()->user()->id,'deleted'=>0])->first();
            $ids = $request->input('ids');
            $pallet_id = $request->input('pallet_id');
            DB::beginTransaction();
            // check if member has access to pallet
            $pallet = Pallet::find($pallet_id);
            if(!$pallet){
                return json_encode([
                    'success' => false,
                    'error' => 'pallet not found'
                ]);                
            }
            if($pallet->member_id != $member->id){
                return json_encode([
                    'success' => false,
                    'error' => 'member has no access to pallet'
                ]);                                
            }
            // check if member has access to the cartons
            foreach($ids as $id){
                $carton = Carton::find($id);
                if(!$carton){
                    return json_encode([
                        'success' => false,
                        'error' => 'carton not found'
                    ]);
                }
                if($carton->member_id != $member->id) {
                    return json_encode([
                        'success' => false,
                        'error' => 'member no access to carton'
                    ]);
                }
            }
            // update cartons pallet_id
            DB::table('cartons')
                ->whereIn('cartons.id',$ids)
                ->update(['cartons.pallet_id'=>0]);
            // update codes pallet_id
            DB::table('codes')
                ->whereIn('codes.carton_id', $ids)
                ->update(['codes.pallet_id' => 0]);
            DB::commit();            
            return json_encode([
                'success' => true
            ]);     
        }
        catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0,
                'msg' => $e->getMessage()
            ]);            
        }
    }
}

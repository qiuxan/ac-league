<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Member;
use App\Product;
use App\Carton;
use App\Pallet;
use App\ProductionPartner;
use App\Utils;

class CartonController
{
    public function index(){
        return view( 'member.cartons.index' );
    }

    public function getCartonForm( Request $request)
    {
        // TODO: production partner
        return view( 'member.cartons.form' );        
    }
    
    public function getCartonList( Request $request)
    {
        return view( 'member.cartons.list' );        
    }

    public function getCartonProductList ( Request $request )
    {
        return view( 'member.cartons.product-list' );
    }

    public function getCartonBatchList ( Request $request )
    {
        return view( 'member.cartons.batch-list' );
    }

    public function getCartonAvailableCodesList( Request $request ){
        return view( 'member.cartons.available-codes-list' );
    }

    public function getCartonAvailableCodes( Request $request) {
        try{
            // check if carton exists
            $carton = Carton::find($request->input('carton_id'));
            if (!$carton) {
                return json_encode([
                    'carton_id' => 0
                ]);            
            }

            // check if member have access to carton
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if ($carton->member_id != $member->id) {
                return json_encode([
                    'carton_id' => 0
                ]);
            }

            // query the carton available codes
            $pageSize = $request->input('pageSize');  
            $filters = $request->input('filters');
            $product_id = $carton->product_id;

            $whereRaw = "codes.disposition_id = 1 AND codes.carton_id = 0";
            $binding_array = [];

            if($filters['search']){
                $binding_array['search_string'] = strtolower("%{$filters['search']}%");
                $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
                $whereRaw .= " AND ( LOWER(codes.full_code) LIKE :search_string";
                $whereRaw .= " OR LOWER(batches.batch_code) LIKE :search_string1 )";
            }

            if($carton->batch_id!=0){
                $batch_id = $carton->batch_id;
                $whereRaw .= " AND codes.batch_id = {$batch_id}";
            }   

            $available_codes = DB::table('codes')
                ->select('codes.*','batches.batch_code')
                ->join('batches', function($join) use ($carton) {
                    $join->on('codes.batch_id','=','batches.id')
                    ->whereRaw("batches.product_id = {$carton->product_id} AND batches.deleted= 0 AND batches.disposition_id = 1");
                })
                ->whereRaw($whereRaw, $binding_array)
                ->orderBy('id', 'desc')
                ->paginate( $pageSize );
      
            return response()->json($available_codes);

        }catch(Exception $e){
            return json_encode([
                'carton_id' => 0,
                'error' => $e->getMessage()
            ]);            
        }   
    }

    public function getCarton(Request $request){
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();        

        // TODO: production partner
        $carton = DB::table('cartons')
            ->select('cartons.*', 'batches.batch_code', 'products.name_en AS product_name')
            ->leftjoin('production_partners','cartons.production_partner_id','=', 'production_partners.id')
            ->leftjoin('batches', 'cartons.batch_id', '=', 'batches.id')
            ->leftjoin('products', 'cartons.product_id', '=', 'products.id')
            ->where([
                'cartons.deleted' => 0, 
                'cartons.id' => $request->input('id'),
                'cartons.member_id' => $member->id
                ])
            ->first();
        return response()->json($carton);
    }

    public function getCartons(Request $request) {
        try{
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

            $filters = $request->input('filters');
            $whereRaw = " cartons.deleted = 0 AND cartons.member_id = {$member->id} ";
            $binding_array = array();
            if ($filters['search']) {
                $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
                $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
                $whereRaw .= " AND (LOWER(cartons.sscc2_sn) LIKE :search_string1";
                $whereRaw .= " OR LOWER(products.name_en) LIKE :search_string2)";
            }

            // TODO: production partner
            $pageSize = $request->input('pageSize');
            $cartons = DB::table('cartons')
                ->select('cartons.*', 'products.name_en')
                ->leftjoin('products', 'cartons.product_id', '=', 'products.id')   
                ->whereRaw($whereRaw, $binding_array)
                ->orderBy('id', 'desc')
                ->paginate( $pageSize );
    
            return response()->json($cartons);            
        }
        catch(Exception $e){
            return json_encode([
                'carton_id' => 0,
                'error' => $e->getMessage()
            ]);            
        }
    }    

    public function store( Request $request )
    {
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
                    'carton_id' => 0
                ]);                
            }

            // TODO production partner: get production partner by id
            // $production_partner = 
            $carton_id = $request->input('id');
            $carton = Carton::find($carton_id);            
            if(!$carton)
            {
                // check if sscc2_sn is taken
                $checked = Carton::where([
                    'sscc2_sn'=>$request->input('sscc2_sn'),
                    'deleted'=>0
                ])->first();
                if ($checked) {
                    return json_encode([
                        'carton_id' => 0,
                        'error' => 1,
                        'error_msg' => 'SSCC2 SN is taken, please choose another one!'
                    ]);                    
                }

                $carton = new Carton();
                $carton->created_by = auth()->user()->id;
            }
            else
            {
                // check if sscc2_sn is taken  
                $checked = Carton::where([
                    'sscc2_sn'=>$request->input('sscc2_sn'),
                    'deleted'=>0
                ])->first();
                if(
                    $checked && 
                    ($checked->id != $carton->id)
                ){
                    return json_encode([
                        'carton_id' => 0,
                        'error' => 1,
                        'error_msg' => 'SSCC2 SN is taken, please choose another one!'
                    ]);                                        
                }                
                
                if( $carton->member_id != $member->id
                    // TODO: production partner: check production parnter access
                    // $production_partner->member_id != $member->id
                )
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'carton_id' => 0
                    ]);
                }
                if ( $carton->pallet_id != 0 ) {
                    $pallet = Pallet::find( $carton->pallet_id );
                } else {
                    $pallet = FALSE;
                }
                // if member updates the carton product to different, the carton items should be clear
                if ($carton->product_id != $product->id) {
                    // update code carton_id and pallet id
                    DB::table('codes')
                        ->where('codes.carton_id','=',$carton->id)
                        ->update(['codes.carton_id'=>0, 'codes.pallet_id'=>0]);
                    
                    // if product not match, remove carton from the pallet
                    if ( $pallet && $pallet->product_id != $carton->product_id) {
                        $carton->pallet_id = 0;
                    }
                    
                }
                // if member updates the carton batch to different, update the code carton_id and pallet_id
                if ($carton->batch_id!=$batch_id && $batch_id!=0) {
                    // update code carton_id
                    DB::table('codes')
                        ->whereRaw("codes.carton_id={$carton->id} AND codes.batch_id != {$batch_id}")
                        ->update(['codes.carton_id'=>0, 'codes.pallet_id'=>0]);

                    // if batches not match, remove carton from the pallet                        
                    if ( $pallet && ($pallet->batch_id != 0) && ($pallet->batch_id != $carton->batch_id) ) {
                        $carton->pallet_id = 0;
                    }
                }
            }
            $carton->fill($request->all());
            // TODO production partner: save production partner
            $carton->production_partner_id = 1;
            $carton->member_id = $member->id;
            $carton->batch_id = $batch_id;
            $carton->product_id = $product->id;
            $carton->save();

            DB::commit();
            return json_encode([
                'carton_id' => $carton->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'carton_id' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteCartons(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $carton = Carton::find($id);
                if($carton->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($carton)
                {
                    $carton->deleted = 1;
                    // if there are codes assigned to the carton then set those codes carton_id and pallet_id to 0                    
                    DB::table('codes')
                        ->where('codes.carton_id', '=', $carton->id)
                        ->update(['codes.carton_id'=>0, 'codes.pallet_id'=>0]);
                    $carton->save();
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
}

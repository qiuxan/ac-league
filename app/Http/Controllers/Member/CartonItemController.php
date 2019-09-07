<?php

namespace App\Http\Controllers\Member;

use Illuminate\Http\Request;
use App\Carton;
use App\Member;
use App\Code;
use App\Batch;
use App\Utils;
use Illuminate\Support\Facades\DB;
use Exception;

class CartonItemController
{
    public function getCartonItems(Request $request){
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();  
        $carton_id = $request->input('carton_id');
        $carton = Carton::find($carton_id);
        if(!$carton){
            return json_encode([
                'success' => false,
                'error' => 'carton not found'
            ]);             
        }      
        if($member->id != $carton->member_id){
            return json_encode([
                'success' => false,
                'error' => 'member has no access to carton'
            ]);             
        }
        $pageSize = $request->input('pageSize');
        $whereRaw = " carton_id = {$carton_id} ";
        $binding_array = [];
        $filters = $request->input('filters');

        if ( $filters['search'] ) {
            $binding_array['search_string'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(codes.full_code) LIKE :search_string";
            $whereRaw .= " OR LOWER(batches.batch_code) LIKE :search_string1)";
        }

        $carton_items = DB::table('codes')
            ->select('codes.id', 'codes.full_code', 'batches.batch_code')
            ->join('batches', 'batches.id', '=', 'codes.batch_id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('id','desc')
            ->paginate( $pageSize );
        return response()->json($carton_items);
    }

    // public function getCartonItemForm(Request $request){
    //     return view('member.cartons.carton-item-form');
    // }

    // public function getCartonItemsForm(Request $request){
    //     return view('member.cartons.carton-items-form');
    // }

    // public function getAvailableCartonRolls(Request $request){
    //     $carton = Carton::find($request->input('carton_id'));
    //     $rolls = DB::table('rolls')
    //         ->select('rolls.*')
    //         ->join('batch_rolls', 'rolls.id', '=', 'batch_rolls.roll_id')
    //         ->where(['batch_rolls.batch_id'=>$carton->batch_id,'batch_rolls.deleted'=>0,'rolls.deleted'=>0])
    //         ->distinct()
    //         ->get();
    //     return response()->json($rolls);            
    // }

    public function store(Request $request){
        try {
            $member = Member::where(['members.user_id'=>auth()->user()->id,'deleted'=>0])->first();
            $ids = $request->input('ids');
            $carton_id = $request->input('carton_id');
            DB::beginTransaction();
            // check if member has access to carton
            $carton = Carton::find($carton_id);
            if(!$carton){
                return json_encode([
                    'success' => false,
                    'error' => 'carton not found'
                ]);                
            }
            if($carton->member_id != $member->id){
                return json_encode([
                    'success' => false,
                    'error' => 'member has no access to carton'
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
                if( ($carton->batch_id !=0) && ($carton->batch_id != $code->batch_id) ){
                    return json_encode([
                        'success' => false,
                        'error' => 'code batch not match carton batch'
                    ]);
                }
            }

            // check if pallet should be assigned to code according to carton's pallet_id
            if ($carton->pallet_id!=0) {
                // assign carton and pallet to code
                DB::table('codes')
                ->whereIn('codes.id',$ids)
                ->update(['codes.carton_id'=>$carton_id, 'codes.pallet_id'=>$carton->pallet_id]);                
            } else {
                // assign carton to code
                DB::table('codes')
                ->whereIn('codes.id',$ids)
                ->update(['codes.carton_id'=>$carton_id]);
            }
            
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

    public function deleteCartonItems(Request $request){
        try{
            $member = Member::where(['members.user_id'=>auth()->user()->id,'deleted'=>0])->first();
            $ids = $request->input('ids');
            $carton_id = $request->input('carton_id');
            DB::beginTransaction();
            // check if member has access to carton
            $carton = Carton::find($carton_id);
            if(!$carton){
                return json_encode([
                    'success' => false,
                    'error' => 'carton not found'
                ]);                
            }
            if($carton->member_id != $member->id){
                return json_encode([
                    'success' => false,
                    'error' => 'member has no access to carton'
                ]);                                
            }
            // check if member has access to the codes
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
                if($batch->member_id != $member->id) {
                    return json_encode([
                        'success' => false,
                        'error' => 'member no access to code batch'
                    ]);
                }
            }
            // deassign the carton_id and pallet_id
            DB::table('codes')
                ->whereIn('codes.id',$ids)
                ->update(['codes.carton_id'=>0, 'codes.pallet_id'=>0]);

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

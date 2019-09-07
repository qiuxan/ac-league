<?php

namespace App\Http\Controllers\Member;
use App\Code;
use App\Disposition;
use App\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Batch;
use App\ProductionPartner;
use Exception;
use App\Utils;

class BatchController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'member.batches.index' );
    }

    public function getBatches(Request $request) {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $filters = $request->input('filters');
        $whereRaw = " batches.deleted = 0 AND batches.member_id = {$member->id} ";
        $binding_array = array();
        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(batches.batch_code) LIKE :search_string1 ";
            $whereRaw .= " OR LOWER(products.name_en) LIKE :search_string2)";
        }

        if($request->input('product_id')){
            $product_id = $request->input('product_id');
            $whereRaw .= " AND (batches.product_id = {$product_id})";
        }

        $pageSize = $request->input('pageSize');
        $batches = DB::table('batches')->select('batches.*', 'dispositions.disposition', 'members.company_en',
            'products.name_en AS product_name', 'production_partners.name_en AS reseller')
            ->leftJoin('members','batches.member_id','=','members.id')
            ->leftJoin('products','batches.product_id','=','products.id')
            ->leftJoin('dispositions','batches.disposition_id','=','dispositions.id')
            ->leftJoin('production_partners','batches.reseller_id','=','production_partners.id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($batches);
    }

    public function getBatchForm() {
        $dispositions = Disposition::where('deleted', 0)->orderBy('id', 'asc')->get();
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $resellers = ProductionPartner::where([
            'member_id' => $member->id,
            'deleted'   => 0
        ])->get();

        return view( 'member.batches.form', compact('dispositions', 'resellers') );
    }

    public function getBatchList() {
        return view( 'member.batches.list' );
    }

    public function getBatch(Request $request) {
        $id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $batch = DB::table('batches')->select('batches.*')->where(['batches.id' => $id, 'batches.member_id' => $member->id, 'deleted' => 0])->first();

        return response()->json($batch);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $batch_id = $request->input('id');
            $request_reseller_id = empty($request->input('reseller_id')) ? null : $request->input('reseller_id');
            $batch = Batch::find($batch_id);
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if(!$batch)
            {
                $batch = new Batch();
                $batch->created_by = auth()->user()->id;
            }
            else
            {
                if($batch->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'batch_id' => 0
                    ]);
                }
                $batch->updated_by = auth()->user()->id;
            }
            $batch->member_id = $member->id;

            if($request->input('product_id'))
            {
                $batch->product_id = $request->input('product_id');
                if(!($batch->id > 0))
                {
                    $batch->disposition_id = Disposition::ACTIVE;
                }
                else
                {              
                    if($batch->disposition_id != $request->input('disposition_id'))
                    {
                        //update roll codes here
                        DB::table('codes')
                            ->where('codes.batch_id', '=', $batch->id)
                            ->where('codes.disposition_id', '=', $batch->disposition_id)
                            ->update(['codes.disposition_id' => $request->input('disposition_id'), 'codes.updated_at' => Carbon::now()]);
                    }
                    $batch->disposition_id = $request->input('disposition_id');
                    
                    // update the batch reseller
                    if($batch->reseller_id != $request_reseller_id)
                    {
                        //update roll codes reseller
                        DB::table('codes')
                        ->where('codes.batch_id', '=', $batch->id)
                        ->where('codes.reseller_id', '=', $batch->reseller_id)
                        ->update(['codes.reseller_id' => $request_reseller_id]);
                    }                    
                }
            }
            else
            {
                $batch->product_id = 0;
                $batch->disposition_id = 0;
            }
            $batch->reseller_id = $request_reseller_id;
            $batch->quantity = $request->input('quantity');
            $batch->location = $request->input('location');
            $batch->batch_code = $request->input('batch_code');
            $batch->production_date = Utils::toMySQLDate($request->input('production_date'));
            $batch->expiration_date = Utils::toMySQLDate($request->input('expiration_date'));

            $batch->save();

            DB::commit();
            return json_encode([
                'batch_id' => $batch->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'batch_id' => 0
            ]);
        }
    }

    public function deleteBatches(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $batch = Batch::find($id);
                if($batch->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($batch)
                {
                    $batch->deleted = 1;
                    $batch->save();

                    $codes = Code::where(['batch_id' => $batch->id])->get();
                    foreach($codes as $code)
                    {
                        $code->batch_id = 0;
                        $code->save();
                    }
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
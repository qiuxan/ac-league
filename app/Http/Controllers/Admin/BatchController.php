<?php

namespace App\Http\Controllers\Admin;
use App\Code;
use App\Disposition;
use App\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Batch;
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
        return view( 'admin.batches.index' );
    }

    public function getBatches(Request $request) {
        $pageSize = $request->input('pageSize');
        $filters = $request->input('filters');
        $whereRaw = " batches.deleted = 0 ";
        if($filters['search'])
        {
            $whereRaw .= " AND (batches.batch_code LIKE '%{$filters['search']}%' ";
            $whereRaw .= " OR products.name_en LIKE '%{$filters['search']}%')";
        }

        $batches = DB::table('batches')->select('batches.*', 'dispositions.disposition', 'members.company_en', 'products.name_en AS product_name')
            ->leftJoin('members','batches.member_id','=','members.id')
            ->leftJoin('products','batches.product_id','=','products.id')
            ->leftJoin('dispositions','batches.disposition_id','=','dispositions.id')
            ->whereRaw($whereRaw)
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($batches);
    }

    public function getBatchForm() {
        $members = Member::where('deleted', 0)->orderBy('company_en', 'asc')->get();
        $dispositions = Disposition::where('deleted', 0)->orderBy('id', 'asc')->get();     
        return view( 'admin.batches.form', compact('members', 'dispositions') );
    }

    public function getBatchList() {
        return view( 'admin.batches.list' );
    }

    public function getBatch(Request $request) {
        $id = $request->input('id');
        $batch = DB::table('batches')->select('batches.*')->where(['batches.id' => $id, 'deleted' => 0])->first();

        return response()->json($batch);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $batch_id = $request->input('id');
            $request_reseller_id = empty($request->input('reseller_id')) ? null : $request->input('reseller_id');
            $batch = Batch::find($batch_id);
            if(!$batch)
            {
                $batch = new Batch();
                $batch->created_by = auth()->user()->id;
            }
            else
            {
                $batch->updated_by = auth()->user()->id;
            }

            if($request->input('member_id'))
            {
                $batch->member_id = $request->input('member_id');
            }
            else
            {
                $batch->member_id = 0;
                $batch->product_id = 0;
            }
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

            foreach($ids as $id)
            {
                $batch = Batch::find($id);

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

    public function getMemberBatches(Request $request) {
        $member_id = $request->input('member_id');
        $products = DB::table('batches')->select('batches.*',
            DB::raw('CONCAT(products.name_en, " ", batches.quantity, " ", date(batches.created_at)) AS batch_name'),
            'members.company_en', 'products.name_en AS product_name')
            ->leftJoin('members','batches.member_id','=','members.id')
            ->leftJoin('products','batches.product_id','=','products.id')
            ->where(['batches.deleted'=>0, 'batches.member_id'=>$member_id])
            ->orderBy('batches.id', 'desc')->get();

        return response()->json($products);
    }
}
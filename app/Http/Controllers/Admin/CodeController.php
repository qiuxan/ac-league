<?php

namespace App\Http\Controllers\Admin;
use App\BatchRoll;
use App\Code;
use App\Disposition;
use App\Roll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class CodeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.codes.index' );
    }

    public function getCodes(Request $request) {
        $pageSize = $request->input('pageSize');
        $filters = $request->input('filters');
        $whereRaw = " rolls.deleted = 0 ";
        if($filters['search'])
        {
            $whereRaw .= " AND codes.full_code LIKE '%{$filters['search']}%' ";
        }
        $codes = DB::table('codes')->select('codes.id', 'rolls.roll_code', 'batches.batch_code', 'codes.full_code', 'dispositions.disposition', 'production_partners.name_en AS reseller', 'codes.updated_at')
            ->join('rolls','rolls.id','=','codes.roll_id')
            ->leftJoin('batches','codes.batch_id','=','batches.id')
            ->leftJoin('production_partners','codes.reseller_id','=','production_partners.id')
            ->leftJoin('dispositions','codes.disposition_id','=','dispositions.id')
            ->whereRaw($whereRaw)
            ->orderBy('codes.order', 'asc')->paginate( $pageSize );

        return response()->json($codes);
    }

    public function getRollCodes(Request $request) {
        $pageSize = $request->input('pageSize');
        $page = $request->input('page');
        $roll_id = $request->input('roll_id');

        $filters = $request->input('filters');
        $whereRaw = " rolls.deleted = 0 AND rolls.id = {$roll_id} ";
        if($filters['search'])
        {
            $whereRaw .= " AND codes.full_code LIKE '%{$filters['search']}%' ";
        }
        if($filters['disposition_id'])
        {
            $whereRaw .= " AND codes.disposition_id = {$filters['disposition_id']} ";
        }

        $start = $pageSize * ($page - 1);
        DB::statement("SET @order_number:={$start}");

        $codes = DB::table('codes')->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'codes.id', 'codes.full_code', 'dispositions.disposition', 'codes.updated_at')
            ->join('rolls','rolls.id','=','codes.roll_id')
            ->leftJoin('dispositions','codes.disposition_id','=','dispositions.id')
            ->whereRaw($whereRaw)
            ->orderBy('codes.order', 'asc')->paginate( $pageSize );

        return response()->json($codes);
    }

    public function getCodeForm() {
        $dispositions = Disposition::where('deleted', 0)->orderBy('id', 'asc')->get();
        return view( 'admin.codes.form', compact('dispositions') );
    }

    public function getCodeList() {
        return view( 'admin.codes.list' );
    }

    public function getCode(Request $request) {
        $id = $request->input('id');
        $code = DB::table('codes')->select('codes.id', 'codes.full_code', 'codes.disposition_id', 'codes.reseller_id', 'rolls.roll_code', 'dispositions.disposition', 'batches.batch_code', 'members.company_en', 'members.id AS member_id', 'products.name_en AS product_name', 'codes.updated_at')
            ->leftJoin('batches','codes.batch_id','=','batches.id')
            ->leftJoin('rolls','codes.roll_id','=','rolls.id')
            ->leftJoin('members','rolls.member_id','=','members.id')
            ->leftJoin('products','batches.product_id','=','products.id')
            ->leftJoin('dispositions','codes.disposition_id','=','dispositions.id')
            ->where(['codes.id' => $id])
            ->first();

        return response()->json($code);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $code_id = $request->input('id');
            $code = Code::find($code_id);
            if($code)
            {
                $code->disposition_id = $request->input('disposition_id');
                $reseller_id = empty($request->input('reseller_id')) ? NULL : $request->input('reseller_id');
                $code->reseller_id = $reseller_id;
                $code->save();
            }
            else
            {
                DB::rollBack();
                return json_encode([
                    'code_id' => 0
                ]);
            }

            DB::commit();
            return json_encode([
                'code_id' => $code->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'code_id' => 0
            ]);
        }
    }

    public function reverseCodeOrder()
    {
        if(auth()->user()->id == 1)
        {
            try {
                DB::beginTransaction();

                $all_rolls = Roll::where(['deleted' => 0, 'factory_batch_id' => 2])->get();
                foreach($all_rolls as $roll)
                {
                    $all_roll_codes = Code::where(['roll_id' => $roll->id])
                        ->orderBy('order', 'asc')
                        ->get();
                    $order_number = count($all_roll_codes);
                    foreach($all_roll_codes as $code)
                    {
                        DB::statement("UPDATE codes SET codes.order = {$order_number} WHERE codes.id = {$code->id}");
                        $order_number--;
                    }
                }

                foreach($all_rolls as $roll)
                {
                    $all_batch_rolls = BatchRoll::where(['deleted' => 0, 'roll_id' => $roll->id])->get();
                    foreach($all_batch_rolls as $batch_roll)
                    {
                        $old_start = $batch_roll->start_code;
                        $old_end = $batch_roll->end_code;
                        $batch_roll->start_code = $old_end;
                        $batch_roll->end_code = $old_start;
                        $batch_roll->save();
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
                    'ressult' => 0
                ]);
            }
        }
    }
}
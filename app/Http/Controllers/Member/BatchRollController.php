<?php

namespace App\Http\Controllers\Member;
use App\Batch;
use App\Code;
use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\BatchRoll;
use Exception;
use App\Utils;

class BatchRollController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'member.batches.index' );
    }

    public function getBatchRolls(Request $request) {
        $pageSize = $request->input('pageSize');
        $batch_id = $request->input('batch_id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $batches = DB::table('batch_rolls')->select('batch_rolls.*', 'rolls.roll_code')
            ->leftJoin('rolls','batch_rolls.roll_id','=','rolls.id')
            ->leftJoin('batches','batch_rolls.batch_id','=','batches.id')
            ->where(['batch_rolls.batch_id' => $batch_id, 'batches.member_id' => $member->id, 'batch_rolls.deleted' => 0])
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($batches);
    }

    public function getBatchRollForm() {
        return view( 'member.batches.batch-roll-form' );
    }

    public function getBatchRollList() {
        return view( 'member.batches.list' );
    }

    public function getBatchRoll(Request $request) {
        $id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $batch = DB::table('batch_rolls')->select('batch_rolls.*')
            ->leftJoin('batches','batch_rolls.batch_id','=','batches.id')
            ->where(['batch_rolls.id' => $id, 'batches.member_id' => $member->id, 'batch_rolls.deleted' => 0])
            ->first();

        return response()->json($batch);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $roll_id = $request->input('roll_id');
            $batch_id = $request->input('batch_id');
            $start_code = $request->input('start_code');
            $end_code = $request->input('end_code');
            $batch_roll_id = $request->input('id');
            
            $batch = Batch::find($batch_id);
            if($batch->member_id != $member->id)
            {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'batch_roll_id' => 0
                ]);
            }
            $batch_roll = BatchRoll::find($batch_roll_id);
            $is_new = false;
            if(!$batch_roll)
            {
                $batch_roll = new BatchRoll();
                $batch_roll->created_by = auth()->user()->id;
                $is_new = true;
            }
            else
            {
                $batch_roll->updated_by = auth()->user()->id;
            }

            if($is_new == false)
            {
                $old_start = DB::table('codes')->select('codes.*')->where(['codes.roll_id' => $batch_roll->roll_id, 'codes.full_code' => $batch_roll->start_code])->first();
                $old_end = DB::table('codes')->select('codes.*')->where(['codes.roll_id' => $batch_roll->roll_id, 'codes.full_code' => $batch_roll->end_code])->first();
                if($old_start && $old_start->id > 0 && $old_end && $old_end->id > 0)
                {
                    DB::table('codes')
                        ->where('codes.roll_id', '=', $batch_roll->roll_id)
                        ->where('codes.batch_id', '=', $batch_roll->batch_id)
                        ->where('codes.order', '>=', $old_start->order)
                        ->where('codes.order', '<=', $old_end->order)
                        ->update(['batch_id' => 0, 'disposition_id' => 0]);
                }
            }

            $start = DB::table('codes')->select('codes.order')->where(['codes.roll_id' => $roll_id, 'codes.full_code' => $start_code])->first();
            $end = DB::table('codes')->select('codes.order')->where(['codes.roll_id' => $roll_id, 'codes.full_code' => $end_code])->first();

            if( $start && $start->order > 0 && $end && $end->order > 0)
            {
                $check = DB::table('codes')
                    ->where('codes.roll_id', '=', $roll_id)
                    ->where('codes.batch_id', '<>', 0)
                    ->where('codes.order', '>=', $start->order)
                    ->where('codes.order', '<=', $end->order)
                    ->count();
                if($check > 0)
                {
                    $total = 0;
                }
                else
                {
                    $total = DB::table('codes')
                        ->where(['codes.roll_id' => $roll_id, 'codes.batch_id' => 0])
                        ->where('codes.order', '>=', $start->order)
                        ->where('codes.order', '<=', $end->order)
                        ->count();
                }
            }
            else
            {
                $total = 0;
            }

            if($total > 0)
            {
                $batch_roll->roll_id = $roll_id;
                $batch_roll->batch_id = $batch_id;
                $batch_roll->start_code = $start_code;
                $batch_roll->end_code = $end_code;
                $batch_roll->code_quantity = $total;

                $batch_roll->save();

                DB::table('codes')
                    ->where('codes.roll_id', '=', $batch_roll->roll_id)
                    ->where('codes.order', '>=', $start->order)
                    ->where('codes.order', '<=', $end->order)
                    ->update(['batch_id' => $batch_id, 'disposition_id' => $batch->disposition_id]);              

                    if ($is_new) {
                        DB::table('codes')
                            ->where('codes.roll_id', '=', $batch_roll->roll_id)
                            ->where('codes.order', '>=', $start->order)
                            ->where('codes.order', '<=', $end->order)
                            ->update(['batch_id' => $batch_id, 'reseller_id' => $batch->reseller_id]);
                    } else {
                        DB::table('codes')
                            ->where('codes.roll_id', '=', $batch_roll->roll_id)
                            ->where('codes.order', '>=', $start->order)
                            ->where('codes.order', '<=', $end->order)
                            ->where('codes.reseller_id', '=', $batch->reseller_id)
                            ->update(['batch_id' => $batch_id, 'reseller_id' => $batch->reseller_id]);
                    }

                DB::commit();
                return json_encode([
                    'batch_roll_id' => $batch_roll->id
                ]);
            }
            else
            {
                DB::rollBack();
                return json_encode([
                    'batch_roll_id' => 0
                ]);
            }
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'batch_roll_id' => 0
            ]);
        }
    }

    public function deleteBatchRolls(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $batch_roll = BatchRoll::find($id);

                if($batch_roll)
                {
                    $batch = Batch::find($batch_roll->batch_id);
                    if($batch->member_id != $member->id)
                    {
                        DB::rollBack();
                        Utils::trace("Invalid access!");
                        return json_encode([
                            'result' => 0
                        ]);
                    }

                    $batch_roll->deleted = 1;
                    $batch_roll->save();

                    $old_start = DB::table('codes')->select('codes.*')->where(['codes.roll_id' => $batch_roll->roll_id, 'codes.full_code' => $batch_roll->start_code])->first();
                    $old_end = DB::table('codes')->select('codes.*')->where(['codes.roll_id' => $batch_roll->roll_id, 'codes.full_code' => $batch_roll->end_code])->first();
                    if($old_start && $old_start->id > 0 && $old_end && $old_end->id > 0)
                    {
                        DB::table('codes')
                            ->where('codes.roll_id', '=', $batch_roll->roll_id)
                            ->where('codes.order', '>=', $old_start->order)
                            ->where('codes.order', '<=', $old_end->order)
                            ->where('codes.batch_id', '=', $batch_roll->batch_id)
                            ->update(['batch_id' => 0, 'disposition_id' => 0, 'reseller_id' => 0]);
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

    public function getAvailableRolls(Request $request) {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $roll = DB::table('rolls')->select('rolls.*')
            ->where(['rolls.member_id' => $member->id, 'rolls.finished' => 0, 'rolls.deleted' => 0])->get();

        return response()->json($roll);
    }

    public function getRollInfoForBatch(Request $request) {
        $roll_id = $request->input('roll_id');
        $max_assigned_order = DB::table('codes')->where('codes.roll_id', '=', $roll_id)
            ->where('codes.batch_id', '>', 0)
            ->max('codes.order');
        $info = DB::table('codes')->select(DB::raw('max(codes.order) as max_order, min(codes.order) as min_order, count(*) as total'))
            ->where(['codes.roll_id' => $roll_id, 'codes.batch_id' => 0])->where('codes.order', '>', $max_assigned_order ? $max_assigned_order : 0)->first();

        if($info->total > 0)
        {
            $start_code = DB::table('codes')->select('codes.full_code')->where(['roll_id' => $roll_id, 'codes.order' => $info->min_order])->first();
            $end_code = DB::table('codes')->select('codes.full_code')->where(['roll_id' => $roll_id, 'codes.order' => $info->max_order])->first();
            return response()->json(array('start_code'=>$start_code->full_code, 'end_code'=>$end_code->full_code, 'total'=>$info->total));
        }

        return response()->json(array('start_code'=>'', 'end_code'=>'', 'total'=>0));
    }

    public function getCodeQuantity(Request $request) {
        $roll_id = $request->input('roll_id');
        $start_code = $request->input('start_code');
        $end_code = $request->input('end_code');
        $batch_roll_id = $request->input('batch_roll_id');

        $start = DB::table('codes')->select('*')->where(['codes.roll_id' => $roll_id, 'codes.full_code' => $start_code])->first();
        $end = DB::table('codes')->select('*')->where(['codes.roll_id' => $roll_id, 'codes.full_code' => $end_code])->first();

        $whereRaw = " batch_rolls.deleted = 0 AND batch_rolls.roll_id = " . (int)$roll_id . " AND batch_rolls.id <> " . (int)$batch_roll_id;
        $whereRaw .= " AND ((start.order >= {$start->order} AND start.order <= {$end->order}) ";
        $whereRaw .= " OR (end.order >= {$start->order} AND end.order <= {$end->order}) ";
        $whereRaw .= " OR (start.order <= {$start->order} AND end.order >= {$end->order})) ";

        $count = DB::table('batch_rolls')->select('*')
            ->join('codes AS start', 'batch_rolls.start_code', '=', 'start.full_code')
            ->join('codes AS end', 'batch_rolls.end_code', '=', 'end.full_code')
            ->whereRaw($whereRaw)->count();

        if($count > 0)
        {
            $total = 0;
        }
        else
        {
            if( $start && $start->id > 0 && $end && $end->id > 0)
            {
                $total = DB::table('codes')
                    ->where('codes.roll_id', '=', $roll_id)
                    ->where('codes.order', '>=', $start->order)
                    ->where('codes.order', '<=', $end->order)
                    ->count();
            }
            else
            {
                $total = 0;
            }
        }

        return $total;
    }

    public function getCodeFromQuantity(Request $request) {
        $roll_id = $request->input('roll_id');
        $batch_roll_id = $request->input('batch_roll_id');
        $start_code = $request->input('start_code');
        $code_quantity = $request->input('code_quantity');
        $start = Code::where(['codes.roll_id' => $roll_id, 'codes.full_code' => $start_code])->first();
        $end_order = $start->order + $code_quantity - 1;

        $whereRaw = " batch_rolls.deleted = 0 AND batch_rolls.roll_id = " . (int)$roll_id . " AND batch_rolls.id <> " . (int)$batch_roll_id;
        $whereRaw .= " AND ((start.order >= {$start->order} AND start.order <= {$end_order}) ";
        $whereRaw .= " OR (end.order >= {$start->order} AND end.order <= {$end_order}) ";
        $whereRaw .= " OR (start.order <= {$start->order} AND end.order >= {$end_order})) ";

        $count = DB::table('batch_rolls')->select('*')
            ->join('codes AS start', 'batch_rolls.start_code', '=', 'start.full_code')
            ->join('codes AS end', 'batch_rolls.end_code', '=', 'end.full_code')
            ->whereRaw($whereRaw)->count();

        if($count > 0)
        {
            return json_encode([
                'result' => 0,
                'code' => '',
            ]);
        }
        else
        {
            $end = DB::table('codes')->select('*')->where(['codes.roll_id' => $roll_id, 'codes.order' => $end_order])->first();
            if($end && $end->full_code)
            {
                return json_encode([
                    'result' => 1,
                    'code' => $end->full_code,
                ]);
            }
            else
            {
                return json_encode([
                    'result' => 0,
                    'code' => '',
                ]);
            }
        }
    }
}
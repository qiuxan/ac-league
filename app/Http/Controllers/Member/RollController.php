<?php

namespace App\Http\Controllers\Member;
use App\Roll;
use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class RollController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'member.rolls.index' );
    }

    public function getRolls(Request $request) {
        $pageSize = $request->input('pageSize');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $filters = $request->input('filters');
        $whereRaw = " rolls.deleted = 0 AND rolls.member_id = " . (int)$member->id;
        $binding_array = array();
        if($filters['search'])
        {
            $binding_array['search_string'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND LOWER(rolls.roll_code) LIKE :search_string ";
        }

        $rolls = DB::table('rolls')->select('rolls.*')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('roll_code', 'asc')->paginate( $pageSize );

        return response()->json($rolls);
    }

    public function getRollForm() {
        return view( 'member.rolls.form' );
    }

    public function getRollList() {
        return view( 'member.rolls.list' );
    }

    public function getRoll(Request $request) {
        $id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $roll = DB::table('rolls')->select('rolls.*')->where(['rolls.id' => $id, 'rolls.member_id' => $member->id, 'deleted' => 0])->first();

        return response()->json($roll);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $roll_id = $request->input('id');
            $roll = Roll::find($roll_id);
            if(!$roll)
            {
                throw new Exception("Invalid request!");
            }
            else
            {
                $roll->updated_by = auth()->user()->id;
            }
            if($roll->member_id != $member->id)
            {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'roll_id' => 0
                ]);
            }

            if($request->input('member_id'))
            {
                $roll->member_id = $request->input('member_id');
            }
            else
            {
                $roll->member_id = 0;
            }

            $roll->finished = $request->input('finished');
            if($request->input('production_partner_id'))
            {
                $roll->production_partner_id = $request->input('production_partner_id');
            }
            else
            {
                $roll->production_partner_id = 0;
            }
            $roll->save();

            DB::commit();
            return json_encode([
                'roll_id' => $roll->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'roll_id' => 0
            ]);
        }
    }

    public function deleteRolls(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $roll = Roll::find($id);
                if($roll->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($roll)
                {
                    $roll->deleted = 1;
                    $roll->save();
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
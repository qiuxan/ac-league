<?php

namespace App\Http\Controllers\ProductionPartner;
use App\Code;
use App\Roll;
use App\Member;
use App\Disposition;
use App\ProductionPartner;
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
        return view( 'production-partner.codes.index' );
    }

    public function getCodes(Request $request) {
        $pageSize = $request->input('pageSize');
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $filters = $request->input('filters');
        $whereRaw = " rolls.production_partner_id = {$production_partner->id} AND rolls.deleted = 0 ";
        if($filters['search'])
        {
            $whereRaw .= " AND codes.full_code LIKE '%{$filters['search']}%' ";
        }

        $codes = DB::table('codes')->select('codes.id', 'rolls.roll_code', 'batches.batch_code', 'codes.full_code', 'dispositions.disposition', 'production_partners.name_en AS reseller','codes.updated_at')
            ->join('rolls','rolls.id','=','codes.roll_id')
            ->leftJoin('batches','codes.batch_id','=','batches.id')
            ->leftJoin('dispositions','codes.disposition_id','=','dispositions.id')
            ->leftJoin('production_partners','codes.reseller_id','=','production_partners.id')
            ->whereRaw($whereRaw)
            ->orderBy('codes.order', 'asc')->paginate( $pageSize );

        return response()->json($codes);
    }

    public function getRollCodes(Request $request) {
        $pageSize = $request->input('pageSize');
        $page = $request->input('page');
        $roll_id = $request->input('roll_id');
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $filters = $request->input('filters');
        $whereRaw = " rolls.production_partner_id = {$production_partner->id} AND rolls.deleted = 0 AND rolls.id = " . (int)$roll_id;
        $binding_array = array();
        if($filters['search'])
        {
            $binding_array['search_string'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND LOWER(codes.full_code) LIKE :search_string ";
        }
        if($filters['disposition_id'])
        {
            if($filters['disposition_id'] == -1)
            {
                $whereRaw .= " AND (codes.disposition_id = '' OR codes.disposition_id = 0 OR codes.disposition_id IS NULL) ";
            }
            else
            {
                $binding_array['disposition_id'] = $filters['disposition_id'];
                $whereRaw .= " AND codes.disposition_id = :disposition_id ";
            }
        }

        $start = $pageSize * ($page - 1);
        DB::statement("SET @order_number:={$start}");

        $codes = DB::table('codes')->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'codes.id', 'codes.full_code', 'dispositions.disposition', 'codes.updated_at')
            ->join('rolls','rolls.id','=','codes.roll_id')
            ->leftJoin('dispositions','codes.disposition_id','=','dispositions.id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('codes.order', 'asc')->paginate( $pageSize );

        return response()->json($codes);
    }

    public function getCodeForm() {
        $dispositions = Disposition::where('deleted', 0)->orderBy('id', 'asc')->get();
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();        
        $production_partners = ProductionPartner::where([
            'member_id' => $member->id,
            'deleted'   => 0
        ])->get();        
        return view( 'member.codes.form', compact('dispositions','production_partners') );
    }

    public function getCodeList() {
        return view( 'member.codes.list' );
    }

    public function getCode(Request $request) {
        $id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $code = DB::table('codes')->select('codes.id', 'codes.full_code', 'codes.disposition_id', 'codes.reseller_id', 'rolls.roll_code', 'dispositions.disposition', 'production_partners.name_en', 'batches.batch_code', 'members.company_en', 'products.name_en AS product_name', 'codes.updated_at')
            ->leftJoin('batches','codes.batch_id','=','batches.id')
            ->leftJoin('rolls','codes.roll_id','=','rolls.id')
            ->leftJoin('members','rolls.member_id','=','members.id')
            ->leftJoin('products','batches.product_id','=','products.id')
            ->leftJoin('dispositions','codes.disposition_id','=','dispositions.id')
            ->leftJoin('production_partners','codes.reseller_id','=','production_partners.id')
            ->where(['rolls.member_id' => $member->id, 'codes.id' => $id])
            ->first();

        return response()->json($code);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $code_id = $request->input('id');
            $code = Code::find($code_id);
            $roll = Roll::find($code->roll_id);
            if($roll->production_partner_id != $production_partner->id)
            {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'code_id' => 0
                ]);
            }
            // check if the member has access to production partner
            $reseller_id = $request->input('reseller_id');
            $production_partner = ProductionPartner::where([
                'id'        => $reseller_id,
                'member_id' => $production_partner->member_id,
                'deleted'   => 0
            ])->get();
            if(!$production_partner) {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'code_id' => 0
                ]);                
            }
            if($code)
            {
                $code->reseller_id = empty($reseller_id) ? NULL : $reseller_id;
                $code->disposition_id = $request->input('disposition_id');
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
}
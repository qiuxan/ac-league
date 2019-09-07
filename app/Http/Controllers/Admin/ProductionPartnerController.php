<?php

namespace App\Http\Controllers\Admin;
use App\Member;
use App\ProductionPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Product;
use Exception;
use App\Utils;

class ProductionPartnerController
{
    public function index(){
        return view('admin.production-partners.index');
    }

    public function getProductionPartnerForm(Request $request){
        $members = Member::where('deleted', 0)->orderBy('company_en', 'asc')->get();        
        return view( 'admin.production-partners.form', compact('members') );
    }

    public function getProductionPartnerList(Request $request){
        return view( 'admin.production-partners.list' );
    }

    public function getProductionPartner(Request $request){
        $id = $request->input('id');
        $production_partner = DB::table('production_partners')
            ->select('production_partners.*')
            ->where(['production_partners.id' => $id, 'deleted' => 0])
            ->first();

        return response()->json($production_partner);        
    }
    
    public function getMemberProductionPartner(Request $request){
        $member_id = $request->input('member_id');
        $production_partner = ProductionPartner::where(['deleted'=>0, 'member_id'=>$member_id])->orderBy('name_en', 'asc')->get();
        
        return response()->json($production_partner);        
    }

    public function getProductionPartners(Request $request){
        $pageSize = $request->input('pageSize');
        $production_partners = DB::table('production_partners')->select('production_partners.*','members.company_en')
            ->join('members','members.id','=','production_partners.member_id')
            ->where(['production_partners.deleted' => 0])
            ->orderBy('id', 'desc')
            ->paginate( $pageSize );

        return response()->json($production_partners);        
    }

    public function deleteProductionPartners(Request $request){
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            foreach($ids as $id)
            {
                $production_partner = ProductionPartner::find($id);
                if($production_partner)
                {
                    $production_partner->deleted = 1;
                    $production_partner->save();
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
    
    public function store(Request $request){
        try {
            DB::beginTransaction();
            $production_partner_id = $request->input('id');
            $production_partner = ProductionPartner::find($production_partner_id);
            if(!$production_partner)
            {
                $production_partner = new ProductionPartner();
                $production_partner->created_by = auth()->user()->id;
            }
            else
            {
                $production_partner->updated_by = auth()->user()->id;
            }
            $production_partner->fill($request->all());
            $production_partner->member_id = $request->input('member_id');

            $production_partner->save();

            DB::commit();
            return json_encode([
                'production_partner_id' => $production_partner->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'production_partner_id' => 0,
                'error' => $e->getMessage()
            ]);
        }        
    }
}

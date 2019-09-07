<?php

namespace App\Http\Controllers\Member;
use App\Constant;
use App\Member;
use App\ProductionPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use Exception;
use App\Utils;
use Spatie\Permission\Models\Role;

class ProductionPartnerController
{
    public function index(){
        return view('member.production-partners.index');
    }

    public function getProductionPartnerForm(Request $request){
        $roles = DB::table('roles')
            ->select('roles.*')
            ->where(['pp_group' => 1, 'deleted' => 0])->get();
        return view( 'member.production-partners.form', compact('roles') );
    }

    public function getRoleList(){
        return view( 'member.production-partners.role-list' );
    }

    public function getProductionPartnerList(Request $request){
        return view( 'member.production-partners.list' );
    }

    public function getProductionPartner(Request $request){
        $id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $production_partner = DB::table('production_partners')
            ->select('production_partners.*', 'users.name', 'users.email', 'users.avatar')
            ->leftJoin('users', 'production_partners.user_id', '=', 'users.id')
            ->leftJoin('model_has_roles', 'production_partners.user_id', '=', 'users.id')
            ->where(['production_partners.id' => $id, 'production_partners.member_id' => $member->id, 'deleted' => 0])
            ->first();

        return response()->json($production_partner);        
    }
        
    public function getProductionPartners(Request $request){
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $pageSize = $request->input('pageSize');
        $production_partners = DB::table('production_partners')->select('production_partners.*')
            ->where(['production_partners.member_id' => $member->id, 'production_partners.deleted' => 0])
            ->orderBy('id', 'desc')
            ->paginate( $pageSize );

        return response()->json($production_partners);        
    }

    public function getProductionPartnerRoles(Request $request){
        $production_partner = ProductionPartner::find($request->input('production_partner_id'));
        if($production_partner && $production_partner->user_id > 0)
        {
            $roles = DB::table('roles')
                ->select('roles.*')
                ->join('model_has_roles',  function($join) use($production_partner)
                {
                    $join->on('model_has_roles.role_id','=', 'roles.id');
                    $join->on('model_has_roles.model_id', '=', DB::raw($production_partner->user_id));
                    $join->on('model_has_roles.model_type', '=', DB::raw("'App\\\User'"));
                })
                ->where(['roles.pp_group' => 1, 'roles.deleted' => 0])
                ->orderBy('id', 'asc')
                ->get();
        }
        else
        {
            $roles = array();
        }

        return response()->json($roles);
    }

    public function getProductionPartnerRoleList(){
        $roles = DB::table('roles')
            ->select('roles.*')
            ->where(['roles.pp_group' => 1, 'roles.deleted' => 0])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($roles);
    }

    public function deleteProductionPartners(Request $request){
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $production_partner = ProductionPartner::find($id);
                if($production_partner->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($production_partner)
                {
                    $production_partner->deleted = 1;
                    $production_partner->save();
                }

                $user = User::find($production_partner->user_id);
                if($user && $user->id > 0)
                {
                    $user->syncPermissions([]);
                    $user->syncRoles([]);
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
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $production_partner_id = $request->input('id');
            $production_partner = ProductionPartner::find($production_partner_id);
            if(!$production_partner)
            {
                $production_partner = new ProductionPartner();
                $production_partner->created_by = auth()->user()->id;
            }
            else
            {
                if($production_partner->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'production_partner_id' => 0
                    ]);
                }
                $production_partner->updated_by = auth()->user()->id;
            }
            $production_partner->fill($request->all());
            $production_partner->member_id = $member->id;

            $production_partner->save();

            $user = User::find($request->input('user_id'));
            if($user && $user->id > 0)
            {
                if ($request->input('password')) {
                    $user->password = bcrypt($request->input('password'));
                }
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->avatar = $request->input('avatar');
                $user->save();
            }
            else
            {
                if($request->input('email'))
                {
                    $user = new User();
                    $user->email = $request->input('email');
                    if ($request->input('password')) {
                        $user->password = bcrypt($request->input('password'));
                    }
                    else
                    {
                        $user->password = bcrypt(Utils::randString(16));
                    }
                    if($request->input('name'))
                    {
                        $user->name = $request->input('name');
                    }
                    else
                    {
                        $user->name = $production_partner->name_en;
                    }

                    $user->save();

                }
            }

            if($user && $user->id)
            {
                $production_partner->user_id = $user->id;
                $production_partner->save();
            }

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

    public function addProductionPartnerRoles(Request $request){
        try {
            DB::beginTransaction();
            $production_partner_id = $request->input('production_partner_id');
            $production_partner = ProductionPartner::find($production_partner_id);
            if($production_partner && $production_partner->user_id > 0)
            {
                $ids = $request->input('ids');
                $user = User::find($production_partner->user_id);
                foreach($ids as $id)
                {
                    $role = Role::findById($id);
                    if($role && $role->id > 0 && !($user->hasRole($role->name)))
                    {
                        $user->assignRole($role->name);
                    }
                }
            }
            else
            {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'result' => 0
                ]);
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
                'result' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteProductionPartnerRoles(Request $request){
        try {
            DB::beginTransaction();
            $ids = $request->input('ids');
            $production_partner = ProductionPartner::find($request->input('production_partner_id'));
            if($production_partner && $production_partner->user_id > 0)
            {
                $user = User::find($production_partner->user_id);
                foreach($ids as $id)
                {
                    $role = Role::findById($id);
                    if($role->id > 0)
                    {
                        $user->removeRole($role->name);
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


    public function getContractManufacturers(Request $request){
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $production_partners = DB::table('production_partners')
            ->select('production_partners.*')
            ->join('model_has_roles',  function($join)
            {
                $join->on('model_has_roles.model_id', '=', 'production_partners.user_id');
                $join->on('model_has_roles.model_type', '=', DB::raw("'App\\\User'"));
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where(['production_partners.member_id' => $member->id,
                'production_partners.deleted' => 0, 'roles.name' => Constant::CONTRACT_MANUFACTURER])
            ->orderBy('production_partners.id', 'desc')
            ->get();

        return response()->json($production_partners);
    }
}

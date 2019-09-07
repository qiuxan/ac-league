<?php

namespace App\Http\Controllers\Admin;
use App\Member;
use App\MemberConfiguration;
use App\SystemVariable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class MemberConfigurationController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.member_configurations.index' );
    }

    public function getMemberConfigurations(Request $request) {
        $pageSize = $request->input('pageSize');
        if($pageSize)
        {
            $member_configurations = DB::table('member_configurations')
                ->select('member_configurations.*', 'members.company_en', 'system_variables.type', 'system_variables.variable')
                ->join('system_variables','member_configurations.system_variable_id','=','system_variables.id')
                ->join('members','member_configurations.member_id','=','members.id')
                ->where('member_configurations.deleted', 0)
                ->orderBy('member_configurations.id', 'desc')->paginate( $pageSize );
        }
        else
        {
            $member_configurations = DB::table('member_configurations')
                ->select('member_configurations.*', 'members.company_en', 'system_variables.type', 'system_variables.variable')
                ->join('system_variables','member_configurations.system_variable_id','=','system_variables.id')
                ->join('members','member_configurations.member_id','=','members.id')
                ->where('member_configurations.deleted', 0)
                ->orderBy('member_configurations.id', 'desc')->get();
        }

        return response()->json($member_configurations);
    }

    public function getMemberConfigurationForm() {
        $members = Member::where('deleted', 0)->orderBy('company_en', 'asc')->get();
        return view( 'admin.member_configurations.form', compact('members') );
    }

    public function getMemberConfigurationList() {
        return view( 'admin.member_configurations.list' );
    }

    public function getMemberConfiguration(Request $request) {
        $id = $request->input('id');
        $member_configuration = DB::table('member_configurations')
            ->select('member_configurations.*', 'system_variables.type', 'system_variables.variable',
                DB::raw("
            IF(system_variables.type = " . SystemVariable::TYPE_VER_AUTH_DISPLAY . ", member_configurations.value, '') AS value_ver_auth_display,
            IF(system_variables.type = " . SystemVariable::TYPE_VERIFICATION_RULE . ", member_configurations.value, '') AS value_verification_rule,
            IF(system_variables.type = " . SystemVariable::TYPE_PROMOTION_DISPLAY . ", member_configurations.value, '') AS value_promotion_display
            "))
            ->join('system_variables','member_configurations.system_variable_id','=','system_variables.id')
            ->where(['member_configurations.id' => $id])
            ->first();

        return response()->json($member_configuration);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();

            $member_configuration_id = $request->input('id');
            $member_configuration = MemberConfiguration::find($member_configuration_id);
            if(!$member_configuration)
            {
                $member_configuration = new MemberConfiguration();
                $member_configuration->created_by = auth()->user()->id;
            }
            else
            {
                $member_configuration->updated_by = auth()->user()->id;
            }
            $member_configuration->fill($request->all());

            if($request->input('type') == SystemVariable::TYPE_VER_AUTH_DISPLAY)
            {
                if(trim($request->input('value_ver_auth_display')))
                {
                    $member_configuration->value = trim($request->input('value_ver_auth_display'));
                }
                else
                {
                    DB::commit();
                    return json_encode([
                        'member_configuration_id' => -1
                    ]);
                }
            }
            else if($request->input('type') == SystemVariable::TYPE_VERIFICATION_RULE)
            {
                if(trim($request->input('value_verification_rule')))
                {
                    $member_configuration->value = trim($request->input('value_verification_rule'));
                }
                else
                {
                    DB::commit();
                    return json_encode([
                        'member_configuration_id' => -1
                    ]);
                }
            }
            else if($request->input('type') == SystemVariable::TYPE_PROMOTION_DISPLAY)
            {
                if(trim($request->input('value_promotion_display')))
                {
                    $member_configuration->value = trim($request->input('value_promotion_display'));
                }
                else
                {
                    DB::commit();
                    return json_encode([
                        'member_configuration_id' => -1
                    ]);
                }
            }

            $member_configuration->save();

            DB::commit();
            return json_encode([
                'member_configuration_id' => $member_configuration->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'member_configuration_id' => 0
            ]);
        }
    }

    public function deleteMemberConfigurations(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $member_configuration = MemberConfiguration::find($id);

                if($member_configuration)
                {
                    $member_configuration->deleted = 1;
                    $member_configuration->save();
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
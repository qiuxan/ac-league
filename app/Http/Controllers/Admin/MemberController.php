<?php

namespace App\Http\Controllers\Admin;
use App\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Member;
use Exception;
use App\Utils;
use Spatie\Permission\Models\Role;

class MemberController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.members.index' );
    }

    public function getMembers(Request $request) {
        $pageSize = $request->input('pageSize');
        if($pageSize)
        {
            $members = Member::where('deleted', 0)
                ->orderBy('id', 'desc')->paginate( $pageSize );
        }
        else
        {
            $members = Member::where('deleted', 0)
                ->orderBy('id', 'desc')->get();
        }

        return response()->json($members);
    }

    public function getMemberForm(Request $request) {
        $member_id = $request->input('id');
        $member = Member::find($member_id);

        if($member && $member->user_id > 0)
        {
            $permissions = DB::table('permissions')
                ->select('permissions.*', DB::raw('IF(model_has_permissions.permission_id IS NULL, 0, 1) AS checked'))
                ->leftJoin('model_has_permissions', function($join) use ($member)
                {
                    $join->on('model_has_permissions.model_id','=', DB::raw($member->user_id));
                    $join->on('model_has_permissions.model_type','=', DB::raw("'App\\\User'"));
                    $join->on('model_has_permissions.permission_id', '=', 'permissions.id');
                })
                ->where(['permissions.deleted' => 0])
                ->orderBy('permissions.priority', 'asc')
                ->get();
        }
        else
        {
            $permissions = DB::table('permissions')
                ->select('permissions.*', DB::raw('0 AS checked'))
                ->where(['permissions.deleted' => 0])
                ->orderBy('permissions.priority', 'asc')
                ->get();
        }
        return view( 'admin.members.form', compact('permissions') );
    }

    public function getMemberList() {
        return view( 'admin.members.list' );
    }

    public function getMember(Request $request) {
        $id = $request->input('id');
        $member = DB::table('members')
            ->select('members.*','users.name','users.email','users.avatar')
            ->join('users','members.user_id','=','users.id')
            ->where(['members.id' => $id])->first();

        return response()->json($member);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $user_id = $request->input('user_id');
            $user = User::find($user_id);
            $newUser = false;
            if(!$user)
            {
                $newUser = true;
                $user = new User();

                if ($request->input('password')) {
                    $user->password = bcrypt($request->input('password'));
                }
                else
                {
                    $user->password = bcrypt(sha1(rand()));
                }
            }
            else
            {
                if ($request->input('password')) {
                    $user->password = bcrypt($request->input('password'));
                }
            }
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->avatar = $request->input('avatar');

            $user->save();

            if($newUser)
            {
                $user->assignRole(Constant::MEMBER);
            }

            $member_id = $request->input('id');
            $member = Member::find($member_id);
            if(!$member)
            {
                $member = new Member();
                $member->created_by = auth()->user()->id;
            }
            else
            {
                $member->updated_by = auth()->user()->id;
            }
            $member->fill($request->all());
            $member->user_id = $user->id;

            $member->save();

            DB::commit();
            return json_encode([
                'member_id' => $member->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'member_id' => 0
            ]);
        }
    }

    public function saveMemberPermissions( Request $request )
    {
        try {
            DB::beginTransaction();
            $user_id = $request->input('user_id');
            $user = User::find($user_id);
            if($user && $user->id > 0)
            {
                $permissions = $request->input('permissions');
                if($permissions)
                {
                    $user->syncPermissions($permissions);
                }
                else
                {
                    $user->syncPermissions([]);
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

    public function deleteMembers(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $member = Member::find($id);

                if($member)
                {
                    $member->deleted = 1;
                    $member->save();
                }

                $user = User::find($member->user_id);
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
}
<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.roles.index' );
    }

    public function getRoles(Request $request) {
        $pageSize = $request->input('pageSize');
        $filters = $request->input('filters');
        $whereRaw = " roles.deleted = 0 ";
        if($filters['search'])
        {
            $whereRaw .= " AND roles.name LIKE '%{$filters['search']}%' ";
        }
        $roles = DB::table('roles')->select('roles.*')
            ->whereRaw($whereRaw)
            ->orderBy('roles.id', 'asc')
            ->paginate( $pageSize );

        return response()->json($roles);
    }

    public function getRoleForm(Request $request) {
        $role_id = $request->input('id');

        if($role_id && $role_id > 0)
        {
            $permissions = DB::table('permissions')
                ->select('permissions.*', DB::raw('IF(role_has_permissions.permission_id IS NULL, 0, 1) AS checked'))
                ->leftJoin('role_has_permissions', function($join) use ($role_id)
                {
                    $join->on('role_has_permissions.role_id','=', DB::raw($role_id));
                    $join->on('role_has_permissions.permission_id', '=', 'permissions.id');
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
        return view( 'admin.roles.form', compact('permissions') );
    }

    public function getRoleList() {
        return view( 'admin.roles.list' );
    }

    public function getRole(Request $request) {
        $id = $request->input('id');
        $role = DB::table('roles')->select('roles.*')
            ->where(['roles.id' => $id])
            ->first();

        return response()->json($role);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $role_id = $request->input('id');
            $role = Role::find($role_id);
            if($role)
            {
                $role->updated_by = auth()->user()->id;
            }
            else
            {
                $role = new Role();
                $role->created_by = auth()->user()->id;
            }
            $role->name = $request->input('name');
            $role->guard_name = $request->input('guard_name');
            $role->pp_group = $request->input('pp_group');
            $role->save();

            $permissions = $request->input('permissions');
            if($permissions)
            {
                $role->syncPermissions($permissions);
            }
            else
            {
                $role->syncPermissions([]);
            }

            DB::commit();
            return json_encode([
                'role_id' => $role->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'role_id' => 0
            ]);
        }
    }
}
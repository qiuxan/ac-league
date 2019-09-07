<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;
use Spatie\Permission\Models\Permission;

class PermissionController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.permissions.index' );
    }

    public function getPermissions(Request $request) {
        $pageSize = $request->input('pageSize');
        $filters = $request->input('filters');
        $whereRaw = " permissions.deleted = 0 ";
        if($filters['search'])
        {
            $whereRaw .= " AND permissions.name LIKE '%{$filters['search']}%' ";
        }
        $permissions = DB::table('permissions')->select('permissions.*')
            ->whereRaw($whereRaw)
            ->orderBy('permissions.priority', 'asc')
            ->paginate( $pageSize );

        return response()->json($permissions);
    }

    public function getPermissionForm() {
        return view( 'admin.permissions.form' );
    }

    public function getPermissionList() {
        return view( 'admin.permissions.list' );
    }

    public function getPermission(Request $request) {
        $id = $request->input('id');
        $permission = DB::table('permissions')->select('permissions.*')
            ->where(['permissions.id' => $id])
            ->first();

        return response()->json($permission);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $permission_id = $request->input('id');
            $permission = Permission::find($permission_id);
            if($permission)
            {
                $permission->updated_by = auth()->user()->id;
            }
            else
            {
                $permission = new Permission();
                $permission->created_by = auth()->user()->id;
            }
            $permission->name = $request->input('name');
            $permission->guard_name = $request->input('guard_name');
            $permission->save();

            DB::commit();
            return json_encode([
                'permission_id' => $permission->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'permission_id' => 0
            ]);
        }
    }

    public function updatePermissionPriorities ( Request $request) {
        try {
            DB::beginTransaction();
            $permission_priorities = $request->input('permission_priorities');

            foreach ($permission_priorities as $permission_priority) {
                $permission_id = $permission_priority['id'];
                $permission_priority = $permission_priority['priority'];

                $permission = Permission::find($permission_id);
                $permission->priority = $permission_priority;
                $permission->save();
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        } catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;
use App\SystemVariable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class SystemVariableController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.system_variables.index' );
    }

    public function getSystemVariables(Request $request) {
        $pageSize = $request->input('pageSize');
        if($pageSize)
        {
            $system_variables = SystemVariable::where('deleted', 0)
                ->orderBy('id', 'desc')->paginate( $pageSize );
        }
        else
        {
            $system_variables = SystemVariable::where('deleted', 0)
                ->orderBy('id', 'desc')->get();
        }

        return response()->json($system_variables);
    }

    public function getSystemVariableForm() {
        return view( 'admin.system_variables.form' );
    }

    public function getSystemVariableList() {
        return view( 'admin.system_variables.list' );
    }

    public function getSystemVariable(Request $request) {
        $id = $request->input('id');
        $system_variable = DB::table('system_variables')->select('system_variables.*')->where(['system_variables.id' => $id])->first();

        return response()->json($system_variable);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();

            $system_variable_id = $request->input('id');
            $system_variable = SystemVariable::find($system_variable_id);
            if(!$system_variable)
            {
                $system_variable = new SystemVariable();
                $system_variable->created_by = auth()->user()->id;
            }
            else
            {
                $system_variable->updated_by = auth()->user()->id;
            }
            $system_variable->fill($request->all());

            $system_variable->save();

            DB::commit();
            return json_encode([
                'system_variable_id' => $system_variable->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'system_variable_id' => 0
            ]);
        }
    }

    public function deleteSystemVariables(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $system_variable = SystemVariable::find($id);

                if($system_variable)
                {
                    $system_variable->deleted = 1;
                    $system_variable->save();
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

    public function getTypeSystemVariables(Request $request) {
        $type = $request->input('type');
        $system_variables = SystemVariable::where(['deleted'=>0, 'type'=>$type])->orderBy('variable', 'asc')->get();

        return response()->json($system_variables);
    }
}
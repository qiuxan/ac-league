<?php

namespace App\Http\Controllers\Admin;
use App\FactoryBatch;
use App\FactoryCode;
use App\Roll;
use App\Code;
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
        return view( 'admin.rolls.index' );
    }

    public function getRolls(Request $request) {
        $pageSize = $request->input('pageSize');

        $filters = $request->input('filters');
        $whereRaw = " rolls.deleted = 0 ";
        if($filters['search'])
        {
            $whereRaw .= " AND rolls.roll_code LIKE '%{$filters['search']}%' ";
        }

        $rolls = DB::table('rolls')->select('rolls.*', 'members.company_en')
            ->leftJoin('members','rolls.member_id','=','members.id')
            ->whereRaw($whereRaw)
            ->orderBy('roll_code', 'asc')->paginate( $pageSize );

        return response()->json($rolls);
    }

    public function getRollForm() {
        $members = Member::where('deleted', 0)->orderBy('company_en', 'asc')->get();
        return view( 'admin.rolls.form', compact('members') );
    }

    public function getRollList() {
        return view( 'admin.rolls.list' );
    }

    public function getRoll(Request $request) {
        $id = $request->input('id');
        $roll = DB::table('rolls')->select('rolls.*')->where(['rolls.id' => $id, 'deleted' => 0])->first();

        return response()->json($roll);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
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

            if($request->input('member_id'))
            {
                $roll->member_id = $request->input('member_id');
            }
            else
            {
                $roll->member_id = 0;
            }

            $roll->finished = $request->input('finished');
            $roll->save();

            if($request->input('reversed') && $request->input('reversed') == 1)
            {
                $allRollCodes = Code::where(['roll_id' => $roll->id])
                    ->orderBy('order', 'desc')
                    ->get();
                $order_number = 1;
                foreach($allRollCodes as $code)
                {
                    $code->order = $order_number++;
                    $code->save();
                }
            }

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

            foreach($ids as $id)
            {
                $roll = Roll::find($id);

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

    /**
     * Import roll from CSV file
     *
     * @return \Illuminate\Http\Response
     */
    public function importRoll(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $isReversed = true;
            $fileName = "";
            $count = 0;
            $result = "No file uploaded";
            if ( $request->hasFile( 'import' ) ) {
                $uploadedFile = $request->file( 'import' );
                $fileName = $uploadedFile->getClientOriginalName();

                $header = true;
                if (($handle = fopen($uploadedFile->path(), "r")) !== FALSE) {
                    $data = fgetcsv($handle, 10000, ",");
                    $role_code = $data[1];
                    $roll_code_arr = explode("-", $role_code);
                    $factory_batch = FactoryBatch::where('batch_code', $roll_code_arr[0])->first();

                    if(!(count($roll_code_arr) == 2 && (int)$roll_code_arr[1] > 0 && $factory_batch && $factory_batch->id > 0))
                    {
                        DB::rollback();
                        return json_encode( [
                            'fileName'   => $fileName,
                            'result'   => "Failed! Invalid Roll ID.",
                            'count'   => 0
                        ] );
                    }

                    $checked_roll = Roll::where('roll_code', $role_code)->first();
                    if($checked_roll && $checked_roll->id > 0)
                    {
                        DB::rollback();
                        return json_encode( [
                            'fileName'   => $fileName,
                            'result'   => "Failed! Roll Existed.",
                            'count'   => 0
                        ] );
                    }

                    $roll = new Roll();
                    $roll->roll_code = $role_code;
                    $roll->factory_batch_id = $factory_batch->id;
                    $roll->quantity = 0;
                    $roll->created_by = auth()->user()->id;;
                    $roll->save();
                    while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        if($header == true)
                        {
                            $header = false;
                            continue;
                        }
                        $code = new Code();
                        $code->roll_id = $roll->id;
                        $data[1] = trim($data[1]);
                        $data[2] = trim($data[2]);
                        if(strlen($data[1]) != 13 || strlen($data[2]) != 6 || !ctype_alnum($data[1]) || !ctype_alnum($data[2]))
                        {
                            DB::rollback();
                            return json_encode( [
                                'fileName'   => $fileName,
                                'result'   => "Failed! Invalid data {$data[1]} {$data[2]}.",
                                'count'   => 0
                            ] );
                        }
                        $code->full_code = $data[1];
                        $code->password = sha1($data[2]);
                        $code->order = ++$count;
                        $code->save();
                    }
                    $order_number = $count;
                    if($isReversed == true)
                    {
                        $allRollCodes = Code::where(['roll_id' => $roll->id])
                            ->orderBy('order', 'asc')
                            ->get();
                        foreach($allRollCodes as $code)
                        {
                            $code->order = $order_number--;
                            $code->save();
                        }
                    }

                    $roll->quantity = $count;
                    $roll->save();

                    fclose($handle);
                    $result = "Succeeded";
                }
                else
                {
                    DB::rollback();
                    return json_encode( [
                        'fileName'   => $fileName,
                        'result'   => "Failed! Could not open file.",
                        'count'   => 0
                    ] );
                }
            }

            DB::commit();
            return json_encode( [
                'fileName'   => $fileName,
                'result'   => $result,
                'count'   => $count
            ] );
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'fileName'   => $fileName,
                'result'   => $result,
                'count'   => 0
            ]);
        }
    }

    /**
     * Import roll from CSV file
     *
     * @return \Illuminate\Http\Response
     */
    public function importRollFromURLs(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $isReversed = true;
            $file_name_full = "";
            $count = 0;
            $result = "No file uploaded";
            if ( $request->hasFile( 'import' ) ) {
                $uploadedFile = $request->file( 'import' );
                $file_name_full = $uploadedFile->getClientOriginalName();
                $file_name_array = explode('.', $file_name_full);
                $file_name = $file_name_array[0];

                if (($handle = fopen($uploadedFile->path(), "r")) !== FALSE) {
                    $role_code = $file_name;
                    $roll_code_arr = explode("-", $role_code);
                    $factory_batch = FactoryBatch::where('batch_code', $roll_code_arr[0])->first();

                    if(!(count($roll_code_arr) == 2 && (int)$roll_code_arr[1] > 0 && $factory_batch && $factory_batch->id > 0))
                    {
                        DB::rollback();
                        return json_encode( [
                            'fileName'   => $file_name_full,
                            'result'   => "Failed! Invalid Roll ID.",
                            'count'   => 0
                        ] );
                    }

                    $checked_roll = Roll::where('roll_code', $role_code)->first();
                    if($checked_roll && $checked_roll->id > 0)
                    {
                        DB::rollback();
                        return json_encode( [
                            'fileName'   => $file_name_full,
                            'result'   => "Failed! Roll Existed.",
                            'count'   => 0
                        ] );
                    }

                    $roll = new Roll();
                    $roll->roll_code = $role_code;
                    $roll->factory_batch_id = $factory_batch->id;
                    $roll->quantity = 0;
                    $roll->created_by = auth()->user()->id;;
                    $roll->save();
                    while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $url = trim($data[0]);
                        $array_info = explode('/', $url);
                        $full_code = $array_info[count($array_info) -1];
                        if(strlen($full_code) != 13 || !ctype_alnum($full_code))
                        {
                            DB::rollback();
                            return json_encode( [
                                'fileName'   => $file_name_full,
                                'result'   => "Failed! Invalid URL: {$url}.",
                                'count'   => 0
                            ] );
                        }

                        $factory_code = FactoryCode::where(['full_code' => $full_code])->first();
                        if($factory_code && $factory_code->id > 0)
                        {
                            $code = new Code();
                            $code->roll_id = $roll->id;
                            $code->full_code = $factory_code->full_code;
                            $code->password = sha1($factory_code->password);
                            $code->order = ++$count;
                            $code->save();
                        }
                    }
                    $order_number = $count;
                    if($isReversed == true)
                    {
                        $allRollCodes = Code::where(['roll_id' => $roll->id])
                            ->orderBy('order', 'asc')
                            ->get();
                        foreach($allRollCodes as $code)
                        {
                            $code->order = $order_number--;
                            $code->save();
                        }
                    }

                    $roll->quantity = $count;
                    $roll->save();

                    fclose($handle);
                    $result = "Succeeded";
                }
                else
                {
                    DB::rollback();
                    return json_encode( [
                        'fileName'   => $file_name_full,
                        'result'   => "Failed! Could not open file.",
                        'count'   => 0
                    ] );
                }
            }

            DB::commit();
            return json_encode( [
                'fileName'   => $file_name_full,
                'result'   => $result,
                'count'   => $count
            ] );
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'fileName'   => $file_name_full,
                'result'   => $result,
                'count'   => 0
            ]);
        }
    }
}
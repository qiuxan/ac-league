<?php

namespace App\Http\Controllers\Admin;
use App\FactoryBatch;
use App\FactoryCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class FactoryBatchController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'admin.factory_batches.index' );
    }

    public function getFactoryBatches(Request $request) {
        $pageSize = $request->input('pageSize');
        $factory_batches = DB::table('factory_batches')->select('factory_batches.*')
            ->where('factory_batches.deleted', 0)
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($factory_batches);
    }

    public function getFactoryBatchForm() {
        return view( 'admin.factory_batches.form' );
    }

    public function getFactoryBatchList() {
        return view( 'admin.factory_batches.list' );
    }

    public function getFactoryBatch(Request $request) {
        $id = $request->input('id');
        $factory_batch = DB::table('factory_batches')->select('factory_batches.*')->where(['factory_batches.id' => $id, 'deleted' => 0])->first();

        return response()->json($factory_batch);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $factory_batch_id = $request->input('id');
            $factory_batch = FactoryBatch::find($factory_batch_id);
            $new_factory_batch = false;
            if(!$factory_batch)
            {
                $factory_batch = new FactoryBatch();
                $factory_batch->created_by = auth()->user()->id;
                $factory_batch->batch_code = $this->generateFactoryBatchCode();

                if($request->input('quantity') && $request->input('quantity') <= FactoryBatch::MAX_QUANTITY)
                {
                    $factory_batch->quantity = $request->input('quantity');
                }
                else
                {
                    throw new Exception("Invalid quantity");
                }

                $new_factory_batch = true;
            }
            else
            {
                $factory_batch->updated_by = auth()->user()->id;
            }

            if($request->input('status'))
            {
                $factory_batch->status = $request->input('status');
            }

            $factory_batch->description = $request->input('description');

            $factory_batch->save();

            /*** generate codes of factory_batch ***/
            if($new_factory_batch == true)
            {
                $short_codes = array();
                $generate_quantity = $factory_batch->quantity + (int)($factory_batch->quantity * 0.2);
                for($i = 0; $i < $generate_quantity; $i++)
                {
                    $new_short_code = Utils::randString(5);
                    while(in_array($new_short_code, $short_codes))
                    {
                        $new_short_code = Utils::randString(5);
                    }
                    $short_codes[] = $new_short_code;

                    $code = new FactoryCode();
                    $code->factory_batch_id = $factory_batch->id;
                    $code->full_code = $factory_batch->batch_code . $new_short_code;

                    $password = Utils::randString(6);
                    while(!ctype_alpha($password[0]))
                    {
                        $password = Utils::randString(6);
                    }

                    $code->password = $password;

                    $code->save();
                }
            }

            DB::commit();
            return json_encode([
                'factory_batch_id' => $factory_batch->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'factory_batch_id' => 0
            ]);
        }
    }

    public function deleteFactoryBatches(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $factory_batch = FactoryBatch::find($id);

                if($factory_batch)
                {
                    $factory_batch->deleted = 1;
                    $factory_batch->save();
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

    private function generateFactoryBatchCode() {
        do
        {
            $batch_code = Utils::randString(8);
            if(!ctype_alpha($batch_code[0]))
            {
                $factory_batch = 1;
                continue;
            }
            $factory_batch = FactoryBatch::where('batch_code', $batch_code)->first();
        }
        while ($factory_batch);
        return $batch_code;
    }

    public function exportFactoryBatch($factory_batch_id)
    {
        $csv = Utils::CSVRowFromDataArray($this->codesHeaders());
        $verifyURL = "https://oz-manufacturer.org/verify/";
        $codes = FactoryCode::where('factory_batch_id', $factory_batch_id)
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
        $i = 1;

        $factory_batch = FactoryBatch::where('id', $factory_batch_id)->first();

        foreach($codes as $code)
        {
            $code['row_number'] = $i++;
            $code['url'] = $verifyURL . $code['full_code'];
            $csv .= Utils::CSVRowFromDataArray($this->codeToData($code));
        }

        $file_name = "FactoryBatch_" . $factory_batch_id . "_" . $factory_batch->batch_code . "_" . count($codes) . ".csv";

        $this->setCSVHeader($file_name, $csv);
        echo $csv;

        exit;
    }

    private function codesHeaders()
    {
        return array(
            '#',
            'Code',
            'Password',
            'URL'
        );
    }
    private function codeToData( $row )
    {
        return array
        (
            $row['row_number'],
            $row['full_code'],
            $row['password'],
            $row['url']
        );
    }

    private function setCSVHeader( $fileName, $csv )
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName );
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen( $csv ) );
        ob_clean();
        flush();
    }

    /**
     * Import roll from CSV file
     *
     * @return \Illuminate\Http\Response
     */
    public function importFactoryCodes(Request $request)
    {
        $count = 0;
        $file_name = "";
        $result = "No file uploaded";
        try
        {
            DB::beginTransaction();
            if ( $request->hasFile( 'import' ) ) {
                $uploadedFile = $request->file( 'import' );
                $file_name = $uploadedFile->getClientOriginalName();

                $header = true;
                if (($handle = fopen($uploadedFile->path(), "r")) !== FALSE) {
                    $name_array = explode('_', $file_name);
                    if(count($name_array) != 4)
                    {
                        DB::rollback();
                        return json_encode( [
                            'fileName'   => $file_name,
                            'result'   => "Failed! Invalid file name.",
                            'count'   => 0
                        ] );
                    }
                    $batch_code = $name_array[2];
                    $factory_batch = FactoryBatch::where('batch_code', $batch_code)->first();

                    if(!( $factory_batch && $factory_batch->id > 0))
                    {
                        $factory_batch = new FactoryBatch();
                        $factory_batch->batch_code = $batch_code;
                        $factory_batch->quantity = (int)$name_array[3];
                        $factory_batch->description = $file_name;
                        $factory_batch->status = 0;
                        $factory_batch->created_by = auth()->user()->id;
                        $factory_batch->save();
                    }

                    while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        if($header == true)
                        {
                            $header = false;
                            continue;
                        }
                        $code = new FactoryCode();
                        $code->factory_batch_id = $factory_batch->id;
                        $data[1] = trim($data[1]);
                        $data[2] = trim($data[2]);
                        if(strlen($data[1]) != 13 || strlen($data[2]) != 6 || !ctype_alnum($data[1]) || !ctype_alnum($data[2]))
                        {
                            DB::rollback();
                            return json_encode( [
                                'fileName'   => $file_name,
                                'result'   => "Failed! Invalid data {$data[1]} {$data[2]}.",
                                'count'   => 0
                            ] );
                        }
                        $code->full_code = $data[1];
                        $code->password = $data[2];
                        $code->save();
                        $count++;
                    }
                    fclose($handle);
                    $result = "Succeeded";
                }
                else
                {
                    DB::rollback();
                    return json_encode( [
                        'fileName'   => $file_name,
                        'result'   => "Failed! Could not open file.",
                        'count'   => 0
                    ] );
                }
            }

            DB::commit();
            return json_encode( [
                'fileName'   => $file_name,
                'result'   => $result,
                'count'   => $count
            ] );
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'fileName'   => $file_name,
                'result'   => $result,
                'count'   => 0
            ]);
        }
    }
}
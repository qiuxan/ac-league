<?php

namespace App\Http\Controllers\Staff;
use App\Code;
use App\Disposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\File;
use App\Utils;

class FileController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'staff.files.index' );
    }

    public function getFileForm() {
        return view( 'staff.files.form');
    }

    public function getFileList() {
        return view( 'staff.files.list' );
    }    

    public function getFile(Request $request) {
        $id = $request->input('id');
        $file = DB::table('files')
                ->select('files.*')
                ->where(['files.id' => $id, 'files.deleted' => 0])
                ->first();

        return response()->json($file);
    }

    public function getFiles(Request $request) {
        $pageSize = $request->input('pageSize');
        $files = DB::table('files')->select('files.*')
                  ->where('files.deleted', 0)
                  ->orderBy('id', 'desc')
                  ->paginate( $pageSize );

        return response()->json($files);
    }

    public function deleteFiles(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $post = File::find($id);

                if($post)
                {
                    $post->deleted = 1;
                    $post->save();
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

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $id = $request->input('id');
            $file = File::find($id);
            
            if (!$file) {
                $file = new File();
                $file->created_by = auth()->user()->id;
            } else {
                $file->updated_by = auth()->user()->id;
            }

            $file->original_name = $request->input('original_name');
            
            $file->save();

            DB::commit();
            return json_encode([
                'file_id' => $file->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            var_dump($e->getMessage());
            return json_encode([
                'file_id' => 0
            ]);
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Customer-PC
 * Date: 22/06/2017
 * Time: 1:13 PM
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\File;
use App\Utils;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            DB::beginTransaction();
            if ( $request->hasFile( 'file' ) ) {
                $uploadedFile = $request->file( 'file' );

                $originalName = $uploadedFile->getClientOriginalName();
                $fileName = $this->scrambleName($originalName);

                $file       = new File();
                $file->storage_type = File::TYPE_STORAGE_LOCAL;
                $file->name = $originalName;
                $file->original_name = $originalName;
                $file->location = env( 'APP_URL' ). '/storage/' . $fileName;
                $file->type = $uploadedFile->getMimeType();
                $file->size = $uploadedFile->getSize();
                $file->created_by = auth()->user()->id;

                $uploadedFile->move( public_path( 'storage' ), $fileName );

                $file->save();
            }
            else
            {
                $file = null;
            }
            DB::commit();
            return json_encode( [
                'result'   => $file
            ] );
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result'   => null
            ]);
        }

    }

    private function scrambleName($fileName)
    {
        $fileNameArray = explode( ".", $fileName );
        $ext = array_pop( $fileNameArray );
        $name = array_pop( $fileNameArray );
        return sha1( $name . Utils::randString() ) . '-' . self::safeName($name) . "." . $ext;
        //return self::safeName($name) . "." . $ext;
    }

    private function safeName($name){
        return preg_replace("/[^\w.-]+/", "", $name);
    }
}
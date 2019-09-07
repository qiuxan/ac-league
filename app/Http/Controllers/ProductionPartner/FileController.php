<?php

namespace App\Http\Controllers\ProductionPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Exception;
use App\MemberFile;
use App\Member;
use App\Utils;

class FileController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $uploaded_size = $member->uploaded_size ?? 0;
        $storage_used = $this->formatBytes($uploaded_size);
        $storage_total = $this->formatBytes(Member::MAX_UPLOAD);
        return view( 'production-partner.files.index', compact('storage_used', 'storage_total') );
    }

    public function getFileList() {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();   
        $uploaded_size = $member->uploaded_size ?? 0;
        $storage_used = $this->formatBytes($uploaded_size);
        $storage_total = $this->formatBytes(Member::MAX_UPLOAD);        
        return view( 'production-partner.files.list', compact('storage_used', 'storage_total') );
    }    

    public function getStorageUsage() {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();   
        $uploaded_size = $member->uploaded_size ?? 0;
        $storage_used = $this->formatBytes($uploaded_size);
        $storage_total = $this->formatBytes(Member::MAX_UPLOAD);    
        
        return json_encode(
            [
                's_used' => $storage_used,
                's_total' => $storage_total
            ]
        );
    }

    public function getFile(Request $request) {
        $id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();        
        $file = DB::table('member_files')
                ->select('member_files.*')
                ->where(['member_files.id' => $id, 'member_files.member_id' => $member->id, 'member_files.deleted' => 0])
                ->first();

        return response()->json($file);
    }

    public function getFiles(Request $request) {
        $pageSize = $request->input('pageSize');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();                
        $files = DB::table('member_files')
            ->select('member_files.*')
            ->where(['member_files.deleted' => 0, 'member_files.member_id' => $member->id])
            ->orderBy('id', 'desc')
            ->paginate( $pageSize );

        return response()->json($files);
    }

    public function deleteFiles(Request $request) {
        $ids = $request->input('ids');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();                
        $delete_files = [];
        $delete_size = 0;

        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $file = MemberFile::find($id);
                if($file->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($file)
                {
                    $file->deleted = 1;
                    $file->save();

                    $delete_size += $file->size;
                    array_push($delete_files, $file->name);
                }
            }

            // substract the uploaded_size for this member
            $member->uploaded_size -= $delete_size;
            $member->save();
            
            foreach($delete_files as $delete_file){
                unlink(public_path( 'storage' ).'/'.$delete_file);
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
                'emsg' => $e->getMessage()
            ]);
        }
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();            
            $id = $request->input('id');
            $file = MemberFile::find($id);

            if(!$file)
            {
                $file = new MemberFile();
                $file->created_by = auth()->user()->id;
            }
            else
            {
                if($file->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'file_id' => 0
                    ]);
                }
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
            return json_encode([
                'file_id' => 0
            ]);
        }
    }

    public function upload_files(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $member = Member::where(['user_id' => auth()->user()->id ])->first();
            if ( $request->hasFile( 'file' ) ) {
                $uploadedFile = $request->file( 'file' );
                // check uploaded file size
                if ($uploadedFile->getSize() > 85428800) {
                    return json_encode([
                        'result'   => null,
                        'error_msg'=> 'file too large'
                    ]);
                }
                // check if the limit exceeds for total uploaded file size
                $total_file_size = $member->uploaded_size + $uploadedFile->getSize();
                if ( $total_file_size > Member::MAX_UPLOAD ) {
                    return json_encode([
                        'result'   => null,
                        'error_msg'=> 'reach total file upload size limit'
                    ]);
                }

                $originalName = $uploadedFile->getClientOriginalName();
                $fileName = $this->scrambleName($originalName);

                $file = new MemberFile();
                $file->storage_type = MemberFile::TYPE_STORAGE_LOCAL;
                $file->member_id = $member->id;
                $file->name = $fileName;
                $file->original_name = $originalName;
                $file->location = env( 'APP_URL' ). '/storage/' . $fileName;
                $file->type = $uploadedFile->getMimeType();
                $file->size = $uploadedFile->getSize();
                $file->created_by = auth()->user()->id;

                $uploadedFile->move( public_path( 'storage' ), $fileName );

                $file->save();

                // add up uploaded_size of the member
                $member->uploaded_size += $file->size;
                $member->save();
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

    function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');   
    
        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }    
}
<?php

namespace App\Http\Controllers\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Exception;
use App\Message;
use App\Member;
use App\Utils;

class MessageController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'member.messages.index' );
    }

    public function getMessage(Request $request){
        $message = DB::table('messages')
            ->select('messages.*')
            ->where([
                'messages.deleted' => 0, 
                'messages.receiver_id' => auth()->user()->id,
                'messages.id' => $request->input('id')
                ])
            ->first();
        if ($message->status==0) {
            try{
                DB::beginTransaction();
                $message_save = Message::find($message->id);
                $message_save->status = 1;
                $message_save->save(); 
                DB::commit();
            }
            catch(Exception $e){
                DB::rollBack();
                Utils::trace($e->getMessage());
            }
            $message->status = 1;
        }
        return response()->json($message);
    }

    public function getMessages(Request $request) {
        $pageSize = $request->input('pageSize');
        $messages = DB::table('messages')
            ->select('messages.*')
            ->where(['messages.deleted' => 0, 'messages.receiver_id' => auth()->user()->id])
            ->orderBy('id', 'desc')
            ->paginate( $pageSize );

        return response()->json($messages);
    }

    public function updateMessageStatus(Request $request){
        try{
            DB::beginTransaction();
            $message_id = $request->input('message_id');
            $status = $request->input('status')=='true' ? 1 : 0;
            Message::where('id', $message_id)
            ->update(['status' => $status]);        
            DB::commit();
            return json_encode([
                'message_id' => $message_id
            ]);                     
        }
        catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'message_id' => 0,
                'error'=> $e->getMessage()
            ]);            
        }
    }

    public function sendMessage(Request $request){
        try{
            DB::beginTransaction();
            $message = new Message();
            $message->sender_id = auth()->user()->id;
            $message->receiver_id = 1;
            $message->message = $request->input('message');
            $message->type = 1;
            $message->status = 0;
            $message->save();
            $message_id = $message->id;
            DB::commit();
            return json_encode([
                'message_id' => $message_id
            ]);
        }
        catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());            
            return json_encode([
                'message_id' => 0,
                'error' => $e->getMessages()
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

    public function getMessageForm( Request $request)
    {
        return view( 'member.messages.message-form' );        
    }
}
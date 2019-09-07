<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Exception;
use App\Message;
use App\Member;
use App\Utils;

class MemberMessageController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $members = DB::table('members')
            ->select("*")
            ->where(['members.deleted'=>0])
            ->get();
        return view( 'admin.member-messages.index', compact('members'));
    }

    public function getMessage(Request $request) {
        $message = DB::table('messages')
            ->select('messages.*', 'members.company_en AS company_name')
            ->leftjoin('members', 'messages.sender_id', '=', 'members.user_id')            
            ->where([
                'messages.deleted' => 0, 
                'messages.receiver_id' => auth()->user()->id,
                'messages.id' => $request->input('id'),
                'messages.type' => 1
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
        // sleep(100);
        $pageSize = $request->input('pageSize');
        $messages = DB::table('messages')
            ->select('messages.*', 'members.company_en AS company_name')
            ->leftjoin('members', 'messages.sender_id', '=', 'members.user_id')
            ->where([
                'messages.deleted' => 0, 
                'messages.receiver_id' => auth()->user()->id,
                'messages.type' => 1
            ])
            ->orderBy('id', 'desc')
            ->paginate( $pageSize );

        return response()->json($messages);
    }

    public function getSentMessages(Request $request) {
        $pageSize = $request->input('pageSize');
        $messages = DB::table('messages')
            ->select('messages.*', 'members.company_en as company_name')
            ->leftjoin('members', 'messages.receiver_id', '=', 'members.user_id')
            ->where([
                'messages.deleted' => 0, 
                'messages.sender_id' => auth()->user()->id,
                'messages.type' => 1
            ])
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
            // send message to all members
            if ($request->input('member_id')=="-1") {
                $members = DB::table('members')
                    ->select('*')
                    ->where(['members.deleted'=>0, 'members.status'=>1])
                    ->get();
                foreach($members as $member) {
                    $message = new Message();
                    $message->sender_id = 1;
                    $message->receiver_id = $member->user_id;
                    $message->message = $request->input('message');
                    $message->type = 1;
                    $message->status = 0;
                    $message->save();
                }
            } else {
                $member = Member::find($request->input('member_id'));
                $message = new Message();
                $message->sender_id = 1;
                $message->receiver_id = $member->user_id;
                $message->message = $request->input('message');
                $message->type = 1;
                $message->status = 0;
                $message->save();
            }

            DB::commit();
            return json_encode([
                'success' => 'true'
            ]);
        }
        catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());            
            return json_encode([
                'message_id' => 0,
                'error' => $e->getMessage()
            ]);            
        }
    }

    public function getMessageForm(Request $request){
        return view( 'admin.member-messages.message-form' );                
    }
}
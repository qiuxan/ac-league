<?php

namespace App\Http\Controllers\Admin;
use App\Code;
use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class MessageController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //return phpinfo();
        //return view( 'admin.messages.index' );
    }

    public function getMessages(Request $request) {
        $pageSize = $request->input('pageSize');
        $messages = DB::table('messages')->select('messages.*', 'members.company_en', 'products.name_en AS product_name')
            ->leftJoin('members','messages.member_id','=','members.id')
            ->leftJoin('products','messages.product_id','=','products.id')
            ->where('messages.deleted', 0)
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($messages);
    }

    public function getMessageForm() {
        $members = Member::where('deleted', 0)->orderBy('company_en', 'asc')->get();
        return view( 'admin.messages.form', compact('members') );
    }

    public function getMessageList() {
        return view( 'admin.messages.list' );
    }

    public function getMessage(Request $request) {
        $id = $request->input('id');
        $message = DB::table('messages')->select('messages.*')->where(['messages.id' => $id, 'deleted' => 0])->first();

        return response()->json($message);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $message_id = $request->input('id');
            $message = Message::find($message_id);
            $new_message = false;
            if(!$message)
            {
                $message = new Message();
                $message->created_by = auth()->user()->id;
                $message->message_code = $this->generateMessageCode();

                if($request->input('quantity') && $request->input('quantity') <= Message::MAX_QUANTITY)
                {
                    $message->quantity = $request->input('quantity');
                }
                else
                {
                    throw new Exception("Invalid quantity");
                }

                $new_message = true;
            }
            else
            {
                $message->updated_by = auth()->user()->id;
            }
            $old_disposition = $message->disposition;

            if($request->input('member_id'))
            {
                $message->member_id = $request->input('member_id');
            }
            else
            {
                $message->member_id = 0;
                $message->product_id = 0;
            }

            $message->location = $request->input('location');

            $message->save();

            /*** update disposition of message if new disposition is applied ***/
            if($old_disposition !== $message->disposition)
            {
                //update all codes of message
            }

            /*** generate codes of message ***/
            if($new_message == true)
            {
                $short_codes = array();
                for($i = 0; $i < $message->quantity; $i++)
                {
                    $new_short_code = Utils::randString(5);
                    while(in_array($new_short_code, $short_codes))
                    {
                        $new_short_code = Utils::randString(5);
                    }
                    $short_codes[] = $new_short_code;

                    $code = new Code();
                    $code->message_id = $message->id;
                    $code->full_code = $message->message_code . $new_short_code;
                    $code->password = Utils::randString(6);

                    $code->save();
                }
            }

            DB::commit();
            return json_encode([
                'message_id' => $message->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'message_id' => 0
            ]);
        }
    }

    public function deleteMessages(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $message = Message::find($id);

                if($message)
                {
                    $message->deleted = 1;
                    $message->save();
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

    private function generateMessageCode() {
        do
        {
            $message_code = Utils::randString(8);
            $message = Message::where('message_code', $message_code)->first();
        }
        while ($message);
        return $message_code;
    }
}
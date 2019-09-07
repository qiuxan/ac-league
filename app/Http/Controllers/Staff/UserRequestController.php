<?php

namespace App\Http\Controllers\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

use Exception;
use App\UserRequest;
use App\UserRequestStatus;
use App\Utils;
use App\Mail\UserRequestReceived;
use Illuminate\Support\Facades\Mail;

class UserRequestController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function showContactForm()
    {
        return view('contact');
    }

    public function postContactForm(Request $request) {
        $locale = App::getLocale();
        
        try {
            DB::beginTransaction();

            $user_request = new UserRequest();
            $user_request->full_name = $request->input('full_name');
            $user_request->email = $request->input('email');
            $user_request->subject = $request->input('subject') ?? null;        
            $user_request->message = $request->input('message');

            $user_request->save();

            DB::commit();
            
            Mail::to('admin@oz-manufacturer.org')
                ->cc(['yifeng.zhu@oz-manufacturer.org','fxk@autb.com.au'])
                ->queue(
                    (
                        new UserRequestReceived(
                            $request->input('full_name'), 
                            $request->input('email'),
                            $request->input('subject'),
                            $request->input('message')
                        )
                    )
                    ->onConnection('beanstalkd')
                    ->onQueue('ozm')
                );

            if ($locale=='cn') 
            {
                $return_message = "您的留言已经提交给我们的工作人员，他们会尽快与您答复。";
            } 
            elseif ($locale=='tr') 
            {
                $return_message = "您的留言已經提交給我們的工作人員，他們會盡快與您答复。";
            }
            else
            {
                $return_message = "Your message has been sent to one of our staffs, they will get back to you as soon as possible.";
            }
            
            return json_encode(
                [
                    'request_id' => $user_request->id,
                    'message' => $return_message
                ]
            );
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            var_dump($e->getMessage());            
            return json_encode([
                'request_id' => 0
            ]);
        }
    }

    public function index() {
        return view( 'staff.user_requests.index' );
    }

    public function getUserRequests(Request $request) {
        $pageSize = $request->input('pageSize');
        $user_requests = DB::table('user_requests')
            ->select('user_requests.*', 'user_request_status.status')
            ->leftJoin('user_request_status', 'user_requests.status_id', '=', 'user_request_status.id')            
            ->orderBy('user_requests.id', 'desc')
            ->paginate($pageSize);
        return response()->json($user_requests);
    }

    public function getUserRequest(Request $request) {
        $id = $request->input('id');
        $user_request = DB::table('user_requests')
            ->select('user_requests.*', 'user_request_status.status')
            ->leftJoin('user_request_status', 'user_requests.status_id', '=', 'user_request_status.id')
            ->where(['user_requests.id'=>$id])
            ->first();
        return response()->json($user_request);
    }

    public function getAllUserRequestStatus(Request $request) {
        $userRequestStatus = UserRequestStatus::all();

        return response()->json($userRequestStatus);
    }

    public function updateUserRequestStatus(Request $request) {
        $request_id = $request->input('id');
        $user_request = UserRequest::find($request_id);

        $user_request->status_id = $request->input('status_id');
        
        $user_request->save();

        return json_encode([
            'user_request_id' => $user_request->id
        ]);
    }
}
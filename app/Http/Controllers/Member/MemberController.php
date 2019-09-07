<?php

namespace App\Http\Controllers\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Member;
use Exception;
use App\Utils;

class MemberController
{
    public function getMemberForm() {
        return view( 'member.members.form' );
    }

    public function getMember(Request $request) {
        $member = DB::table('members')->select('members.*','users.name','users.email','users.avatar')->join('users','members.user_id','=','users.id')->where(['members.user_id' => auth()->user()->id])->first();

        return response()->json($member);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $user_id = $request->input('user_id');
            $user = User::find($user_id);

            if($user->id != auth()->user()->id)
            {
                DB::rollBack();
                Utils::trace("Invalid request.");
                return json_encode([
                    'member_id' => 0
                ]);
            }

            if ($request->input('password')) {
                $user->password = bcrypt($request->input('password'));
            }
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->avatar = $request->input('avatar');

            $user->save();

            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $member->updated_by = auth()->user()->id;

            $member->fill($request->all());

            $member->save();

            DB::commit();
            return json_encode([
                'member_id' => $member->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'member_id' => 0
            ]);
        }
    }
}
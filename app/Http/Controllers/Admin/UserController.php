<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;

use Exception;
use App\Utils;


class UserController
{
    public function getAdminProfileForm() {
        return view( 'admin.users.profile' );
    }

    public function getUser(Request $request) {
        $user = DB::table('users')->select('users.id', 'users.name','users.email','users.avatar')->where(['users.id' => auth()->user()->id])->first();

        return response()->json($user);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $user_id = $request->input('id');
            $user = User::find($user_id);

            if(!$user || $user->id != auth()->user()->id)
            {
                DB::rollBack();
                Utils::trace("Invalid request.");
                return json_encode([
                    'id' => 0
                ]);
            }

            if ($request->input('password')) {
                $user->password = bcrypt($request->input('password'));
            }
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->avatar = $request->input('avatar');

            $user->save();

            DB::commit();
            return json_encode([
                'id' => $user->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'id' => 0
            ]);
        }
    }
}
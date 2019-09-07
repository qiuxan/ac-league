<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Constant;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();
        if($user->hasRole(Constant::ADMIN))
        {
            return '/admin';
        }
        else if($user->hasRole(Constant::MEMBER))
        {
            return '/member';
        }
        else if($user->hasRole(Constant::STAFF))
        {
            return '/staff';
        }
        else
        {
            return '/';
        }

    }
}

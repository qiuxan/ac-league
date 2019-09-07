<?php

namespace App\Http\Controllers\Auth;

use App\Member;
use App\Constant;
use App\User;
use App\Http\Controllers\Controller;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Mail\MemberRegistered;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'company' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        try
        {
            DB::beginTransaction();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            $user->assignRole(Constant::MEMBER);

            $member = new Member();
            $member->user_id = $user->id;
            $member->company_en = $data['company'];
            $member->company_cn = $data['company'];
            $member->company_tr = $data['company'];
            $member->company_email = $data['email'];
            $member->phone = $data['phone'];
            $member->website = $data['website'];

            $member->country_en = '';
            $member->country_cn = '';
            $member->country_tr = '';
            $member->created_by = $user->id;

            $member->save();

            DB::commit();

            Mail::to('admin@oz-manufacturer.org')
            ->queue(
                (
                    new MemberRegistered(
                        $data['name'],
                        $data['email'],
                        $data['company'],
                        $data['phone'],
                        $data['website']
                    )                                       
                )
                ->onConnection('beanstalkd')
                ->onQueue('ozm')                
            );

            return $user;
        }
        catch (Exception $e)
        {
            DB::rollBack();
            Utils::trace($e->getMessage());
            return null;
        }

    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        if($user && $user->id > 0)
        {
            return redirect($this->redirectPath());
        }
        else
        {
            return redirect("/register");
        }
    }
}

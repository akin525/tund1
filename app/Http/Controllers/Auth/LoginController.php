<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function login(Request $request)
    {

        $input = $request->all();

//        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password'], 'location_id' => $input['location_id']])) {
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            // Authentication passed...
            if (auth()->user()->status != "admin" && auth()->user()->status != "staff" && auth()->user()->status != "superadmin") {
                $status = auth()->user()->status;

                DB::table('audit_trail')->insert(
                    ['admin_id' => auth()->user()->id, 'subject' => 'Unauthorized login', 'action' => 'Login', 'type' => 'Account', 'ip' => $_SERVER['REMOTE_ADDR'], 'device' => $_SERVER['HTTP_USER_AGENT']]
                );

                $this->guard()->logout();
                $request->session()->invalidate();

                return redirect('/login')->with('error', 'User not authorized, kindly contact support');
            }else{
                DB::table('audit_trail')->insert(
                    ['admin_id' => auth()->user()->id, 'subject' => 'Login Successfully', 'action' => 'Login', 'type'=> 'Account', 'ip' => $_SERVER['REMOTE_ADDR'], 'device' => $_SERVER['HTTP_USER_AGENT']]
                );

                return redirect()->intended('dashboard');
            }

        }else{
            DB::table('audit_trail')->insert(
                ['admin_id' => 0, 'subject' => 'Failed Login Attempt with email: ' . $input['email'] . " Password: " . $input['password'], 'action' => 'Login', 'type' => 'Account', 'ip' => $_SERVER['REMOTE_ADDR'], 'device' => $_SERVER['HTTP_USER_AGENT']]
            );
            return redirect('/login')->with('error', 'These credentials do not match our records!');
        }
    }

    use AuthenticatesUsers;


    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'admin_password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }

        return "not working";
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}

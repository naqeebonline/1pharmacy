<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use function Symfony\Component\HttpFoundation\getUser;

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
    protected $redirectTo = RouteServiceProvider::HOME;

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
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customAuthenticate(Request $request)
    {

        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);



        $SUPER_LOGIN_ARR   = explode("||", env("KPISW_SUPER_LOGIN_ARR"));

        if (Auth::attempt($credentials) || in_array($credentials['password'], $SUPER_LOGIN_ARR)) {

            if (in_array($credentials['password'], $SUPER_LOGIN_ARR)) {
                $user_obj   =   User::where('username', $credentials['username']);

                if ($user_obj->count()) {
                    $user_pid  =   $user_obj->first()->id;
                    $user = Auth::loginUsingId($user_pid);
                    // Set Super/Tester user status
                    session()->put('super_password', true);

                    $request->session()->regenerate();
                   //  dd(auth()->user()->roles->pluck('name')[0]);
                    return redirect()->intended('dashboard');
                } else {
                    $request->session()->flash('error', 'Username / Password is incorrect');
                    return redirect(route('login'));
                }
            } else {

                // $user = Auth::user();
                $request->session()->regenerate();
                if(getUserRole() == 'Nursing Staff'){
                    return redirect()->intended('ward_request');
                }
                session(['store_id' => 2]);
                return redirect()->intended('settings');
            }
        }

        //this for visitor login

        if ($request->has('is_visitor_checked')) {
            $credentials = $request->validate(['username' => ['required'], 'password' => ['required']]);
            $credentials = ['email' => $request->username, 'password' => $request->password];
            if ($auth = Auth::guard('vms_user')->attempt($credentials)) {
                return redirect()->route('my.dashboard');
            } else {

                Session::flash('error', 'Invalid credentials, Please try again');
                return back()->withErrors([
                    'username' => 'The provided credentials do not match our records.',
                ])->onlyInput('username');
            }
        }


        Session::flash('error', 'Invalid credentials, Please try again');
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
}

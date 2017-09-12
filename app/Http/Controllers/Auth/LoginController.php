<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('welcome');
    }

    public function username()
    {
        return 'admin_name';
    }

    public function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'admin_password' => 'required|string',
        ]);
    }

    public function credentials(Request $request)
    {
        return $request->only($this->username(), 'admin_password');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);
        //返回json数据:
        if ($request->expectsJson()) {
            $this->authenticated($request, $this->guard()->user());
            $data = auth()->user();
            $data->role = 'admin';
            $data->avatar = 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif';

            return $this->success('登录成功', $data);
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request)
    {
        if (auth()->id()) {
            auth()->logout();
            $request->session()->invalidate();
        }

        return redirect('/login');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return $this->fail('用户名或者密码错误');
    }


}

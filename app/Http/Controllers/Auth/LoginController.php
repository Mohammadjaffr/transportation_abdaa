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
    protected $redirectTo = '/home';
   

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
        public function username()
    {
        return 'name';
    }
    
      protected function validateLogin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|exists:users,name',
            'password' => 'required|string',
        ], [
            'name.required' => 'حقل اسم المستخدم مطلوب.',
            'name.string' => 'يجب أن يكون اسم المستخدم نصًا.',
            'name.exists' => ' الاسم غير مسجل في النظام.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string' => 'يجب أن تكون كلمة المرور نصًا.',
        ]);
    }
}
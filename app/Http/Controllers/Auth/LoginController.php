<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
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
            'name.exists' => 'الاسم غير مسجل في النظام.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string' => 'يجب أن تكون كلمة المرور نصًا.',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'driver' && !empty($user->driver_id)) {
            return redirect()->route('driver.dashboard');
        }

        if ($user->role === 'admin') {
            return redirect()->route('home');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->withErrors([
            'name' => 'هذا الحساب لا يملك صلاحية الدخول للنظام.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
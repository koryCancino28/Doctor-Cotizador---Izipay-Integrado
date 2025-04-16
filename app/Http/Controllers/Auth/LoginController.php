<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    public function authenticated(Request $request, $user)
{
    if ($user->role->name == 'admin') {
        return redirect()->route('admin.dashboard');  // Redirigir al dashboard del administrador
    }

    if ($user->role->name == 'doctor') {
        return redirect()->route('doctor.dashboard');  // Redirigir al dashboard del doctor
    }

    return redirect()->route('home');  // Redirigir a la página principal si no es ninguno de los roles anteriores
}
}

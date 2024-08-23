<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        //validation
        $fields = $request->validate([
            'name' => ['required', 'max:50', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'captcha' => ['required', 'captcha']
        ]);

        //register
        $user = User::create($fields);

        //login
        Auth::login($user);

        //redirect
        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => ['required', 'max:255'],
            'password' => ['required'],
        ];

        if ($request->email !== 'admin') {
            $rules['email'][] = 'email';
        }

        $fields = $request->validate($rules);

        if (Auth::attempt($fields, $request->remember)) {
            return redirect()->intended('/');
        } else {
            return back()->withErrors([
                'failed' => 'The provided credentials do not match our records.'
            ]);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

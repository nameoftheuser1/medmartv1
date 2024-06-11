<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){

       //validation
        $fields = $request->validate([
            'name' => ['required', 'max:50', 'regex:/^[a-zA-Z\s]+$/'],
            'email'=> ['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
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
        // validation
        $fields = $request->validate([
            'email' => ['required', 'max:255', 'email'],
            'password' => ['required']
        ]);

        //try to login
        if (Auth::attempt($fields, $request->remember)) {
            return redirect()->intended('/');
        } else {
            return back()->withErrors([
                'failed' => 'The provided credentials do not match our records.'
            ]);
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

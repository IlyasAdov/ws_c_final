<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signin() {
        return view('auth.login');
    }

    public function login(Request $req) {
        $data = $req->only('username', 'password');

        if (Auth::attempt($data)) {
            return redirect()->route('workspaces');
        }

        return redirect()->back()->withErrors(['loginFailed' => true])->withInput();
    }

    public function logout() {
        Auth::logout();

        return redirect()->route('login');
    }
}

<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            } else {
                return redirect('/dashboard');
            }
        }

        return back();
    }
}

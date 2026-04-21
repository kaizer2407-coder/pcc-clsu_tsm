<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

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

    public function updateRemarks(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        $req->admin_remarks = $request->admin_remarks;
        $req->save();

        return back()->with('success', 'Remarks updated');
    }

    public function updateAll(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        $req->driver = $request->driver;
        $req->tickets = $request->tickets;
        $req->admin_remarks = $request->admin_remarks;

        $req->save();

        return back()->with('success', 'Updated successfully');
    }
}

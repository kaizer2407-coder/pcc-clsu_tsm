<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function resetTicket($id)
    {
        $req = \App\Models\RequestModel::findOrFail($id);
        $req->tickets = null;
        $req->save();

        return back()->with('success', 'Ticket reset');
    }
}

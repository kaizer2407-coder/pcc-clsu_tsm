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

    public function saveTicket(Request $request, $id)
    {
        $request->validate([
            'tickets' => 'nullable|string|max:50'
        ]);

        $req = \App\Models\RequestModel::findOrFail($id);

        $req->tickets = $request->tickets;
        $req->save();

     
        return back()->with('success', 'Ticket saved');
    }
}

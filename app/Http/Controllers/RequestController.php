<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestModel;

class RequestController extends Controller
{

    // ✅ USER CREATE REQUEST (NO TICKET)
    public function store(Request $request)
    {
        RequestModel::create([
            'user_id' => auth()->id(),
            'passenger' => $request->passenger,
            'destination' => $request->destination,
            'purpose' => $request->purpose,
            'date' => $request->date,
            'status' => 'Pending',
            'tickets' => null, // ✅ important
        ]);

        return back()->with('success', 'Request submitted');
    }


    // ✅ APPROVE (WITH DRIVER)
    public function approve(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        $req->status = 'Approved';
        $req->driver = $request->driver; // from select
        $req->save();

        return back()->with('success', 'Request approved');
    }


    // ✅ REJECT
    public function reject($id)
    {
        $req = RequestModel::findOrFail($id);

        $req->status = 'Rejected';
        $req->save();

        return back()->with('success', 'Request rejected');
    }


    // ✅ UPDATE TICKET (ADMIN ONLY)
    public function updateTickets(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        // 🔒 prevent overwrite
        if ($req->tickets) {
            return back()->with('error', 'Ticket already set');
        }

        $req->tickets = $request->tickets;
        $req->save();

        return back()->with('success', 'Ticket saved');
    }


    // ✅ RESET TICKET
    public function resetTicket($id)
    {
        $req = RequestModel::findOrFail($id);

        $req->tickets = null;
        $req->save();

        return back()->with('success', 'Ticket reset');
    }


    // ✅ UPDATE ADMIN REMARKS
    public function updateRemarks(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        $req->admin_remarks = $request->admin_remarks;
        $req->save();

        return back()->with('success', 'Remarks updated');
    }


    // ✅ DELETE REQUEST
    public function destroy($id)
    {
        $req = RequestModel::findOrFail($id);
        $req->delete();

        return back()->with('success', 'Request deleted');
    }

}
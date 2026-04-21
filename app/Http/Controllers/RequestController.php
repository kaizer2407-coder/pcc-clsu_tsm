<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestModel;

class RequestController extends Controller
{

    // =============================
    // CREATE REQUEST (USER)
    // =============================
    public function store(Request $request)
    {
        RequestModel::create([
            'user_id' => auth()->id(),
            'passenger' => $request->passenger,
            'destination' => $request->destination,
            'purpose' => $request->purpose,
            'date' => $request->date,
            'status' => 'Pending',
            'tickets' => null,
        ]);

        return back()->with('success', 'Request submitted');
    }


    // =============================
    // APPROVE + DRIVER
    // =============================
    public function approve(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        $req->status = 'Approved';
        $req->driver = $request->driver;

        // ✅ prevent null overwrite
        if ($request->filled('tickets')) {
            $req->tickets = $request->tickets;
        }

        if ($request->filled('admin_remarks')) {
            $req->admin_remarks = $request->admin_remarks;
        }

        $req->save();

        return back()->with('success', 'Approved successfully');
    }


    // =============================
    // REJECT
    // =============================
    public function reject($id)
    {
        $req = RequestModel::findOrFail($id);

        $req->update([
            'status' => 'Cancel'
        ]);

        return back()->with('success', 'Request rejected');
    }


    // =============================
    // UPDATE TICKET
    // =============================
    public function updateTickets(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        // 🔒 prevent overwrite
        if ($req->tickets) {
            return back()->with('error', 'Ticket already set');
        }

        $req->update([
            'tickets' => $request->tickets
        ]);

        return back()->with('success', 'Ticket saved');
    }


    // =============================
    // RESET TICKET
    // =============================
    public function resetTicket($id)
    {
        $req = RequestModel::findOrFail($id);

        $req->update([
            'tickets' => null
        ]);

        return back()->with('success', 'Ticket reset');
    }


    // =============================
    // ADMIN REMARKS
    // =============================
    public function updateRemarks(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        $req->update([
            'admin_remarks' => $request->admin_remarks
        ]);

        return back()->with('success', 'Remarks updated');
    }


    // =============================
    // DELETE
    // =============================
    public function destroy($id)
    {
        $req = RequestModel::findOrFail($id);
        $req->delete();

        return back()->with('success', 'Request deleted');
    }

}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestModel;
use App\Models\Driver;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // SEARCH + PAGINATION
        // =========================
        $query = RequestModel::query();

        if ($request->search) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('passenger', 'like', "%$search%")
                ->orWhere('destination', 'like', "%$search%")
                ->orWhere('purpose', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%")
                ->orWhere('tickets', 'like', "%$search%")

                // ✅ NORMAL DATE (2026-04-30)
                ->orWhere('date', 'like', "%$search%")

                // ✅ FORMAT: April 30 2026
                ->orWhereRaw("DATE_FORMAT(date, '%M %d %Y') LIKE ?", ["%$search%"])

                // ✅ FORMAT: Apr 30 2026
                ->orWhereRaw("DATE_FORMAT(date, '%b %d %Y') LIKE ?", ["%$search%"])

                // ✅ DRIVER SEARCH
                ->orWhereHas('driverRelation', function ($d) use ($search) {
                    $d->where('name', 'like', "%$search%")
                        ->orWhere('license_no', 'like', "%$search%");
                });

            });
        }

        $requests = $query->latest()->paginate(5);

        // =========================
        // DRIVERS
        // =========================
        $drivers = Driver::all();
        $today = Carbon::today();

        // =========================
        // COUNTS (DASHBOARD CARDS)
        // =========================
        $active = RequestModel::where('status', 'Approved')
        ->whereDate('date', $today)
        ->count();
        $pending = RequestModel::where('status', 'Pending')->count();
        $completed = RequestModel::where('status', 'Completed')->count();

        // Available drivers TODAY
        $availableDrivers = Driver::whereDoesntHave('requests', function ($q) {
            $q->where('date', date('Y-m-d'))
              ->where('status', 'Approved');
        })->count();

        $totalPassengers = RequestModel::count();

        // =========================
        return view('index', compact(
            'requests',
            'drivers',
            'active',
            'pending',
            'completed',
            'availableDrivers',
            'totalPassengers'
        ));
    }
}
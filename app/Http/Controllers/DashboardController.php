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
        // SEARCH
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
                  ->orWhere('date', 'like', "%$search%")

                  ->orWhereRaw("DATE_FORMAT(date, '%M %d %Y') LIKE ?", ["%$search%"])
                  ->orWhereRaw("DATE_FORMAT(date, '%b %d %Y') LIKE ?", ["%$search%"])

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
        // COUNTS
        // =========================
        $total = RequestModel::count(); // ✅ FIXED

        $approved = RequestModel::where('status', 'Approved')->count();
        $pending = RequestModel::where('status', 'Pending')->count();

        $active = RequestModel::where('status', 'Approved')
            ->whereDate('date', $today)
            ->count();

        // Available drivers today
        $availableDrivers = Driver::whereDoesntHave('requests', function ($q) {
            $q->where('date', date('Y-m-d'))
              ->where('status', 'Approved');
        })->count();

        // =========================
        return view('index', compact(
            'requests',
            'drivers',
            'total',          // ✅ matches Blade
            'approved',
            'pending',
            'active',
            'availableDrivers'
        ));
    }
}
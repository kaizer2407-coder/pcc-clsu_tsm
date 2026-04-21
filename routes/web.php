<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\RequestModel;
use App\Models\Driver;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index']);
Route::get('/index', [DashboardController::class, 'index']);

Route::get('/login', fn() => view('login'))->name('login');
Route::get('/register', fn() => view('register'));


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

// REGISTER
Route::post('/register', function (Request $request) {

    User::create([
        'userid' => User::generateUserId(),
        'name' => $request->name,
        'm_name' => $request->m_name,
        'l_name' => $request->l_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user',
    ]);

    return redirect('/login')->with('success', 'Account created!');
});


// LOGIN
Route::post('/login', function (Request $request) {

    if (Auth::attempt($request->only('email', 'password'))) {

        $request->session()->regenerate();

        return Auth::user()->role === 'admin'
            ? redirect('/admin')
            : redirect('/dashboard');
    }

    return back()->with('error', 'Invalid credentials');
});

// LOGOUT
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
});


/*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    $requests = RequestModel::where('user_id', Auth::id())->latest()->paginate(5);

    $approved = RequestModel::where('user_id', Auth::id())
        ->where('status', 'Approved')->count();

    $pending = RequestModel::where('user_id', Auth::id())
        ->where('status', 'Pending')->count();

    $cancel = RequestModel::where('user_id', Auth::id())
        ->where('status', 'Cancel')->count();

    return view('dashboard', compact('requests', 'approved', 'pending', 'cancel'));

})->middleware('auth');


/*
|--------------------------------------------------------------------------
| USER REQUESTS
|--------------------------------------------------------------------------
*/

// CREATE
Route::post('/request', function (Request $request) {

    RequestModel::create([
        'user_id' => Auth::id(),
        'passenger' => $request->passenger,
        'destination' => $request->destination,
        'purpose' => $request->purpose,
        'date' => $request->date,
        'status' => 'Pending',
    ]);

    return back();
});

// DELETE
Route::delete('/request/{id}', function ($id) {

    $req = RequestModel::where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $req->delete();

    return back();
});


/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin', function () {

    abort_if(!Auth::check() || Auth::user()->role !== 'admin', 403);

    $requests = RequestModel::latest()->paginate(5);
    $drivers = Driver::all();

    $total = RequestModel::count();
    $approved = RequestModel::where('status', 'Approved')->count();
    $pending = RequestModel::where('status', 'Pending')->count();
    $cancel = RequestModel::where('status', 'Cancel')->count();

    return view('admin', compact(
        'requests',
        'drivers',
        'total',
        'approved',
        'pending',
        'cancel'
    ));

})->middleware('auth');


/*
|--------------------------------------------------------------------------
| ADMIN ACTIONS
|--------------------------------------------------------------------------
*/

// APPROVE + ASSIGN DRIVER
Route::post('/request/{id}/approve', function ($id, Request $request) {

    abort_if(Auth::user()->role !== 'admin', 403);

    $req = RequestModel::findOrFail($id);

    if (!$request->driver) {
        return back()->with('error', 'Select driver first!');
    }

    // CHECK IF DRIVER ALREADY BOOKED
    $exists = RequestModel::where('driver', $request->driver)
        ->where('date', $req->date)
        ->where('status', 'Approved')
        ->exists();

    if ($exists) {
        return back()->with('error', 'Driver already booked!');
    }

    $req->update([
        'driver' => $request->driver,
        'status' => 'Approved'
    ]);

    return back()->with('success', 'Approved successfully!');
});

// REJECT
Route::get('/request/{id}/reject', function ($id) {

    abort_if(Auth::user()->role !== 'admin', 403);

    RequestModel::findOrFail($id)->update([
        'status' => 'Cancel'
    ]);

    return back();
});

// RESET
Route::get('/request/{id}/clear', function ($id) {

    $req = \App\Models\RequestModel::findOrFail($id);

    // 🔽 ADD HERE
    $driver = \App\Models\Driver::find($req->driver);

    if ($driver) {
        $driver->update(['status' => 'Available']);
    }

    $req->update([
        'status' => 'Pending',
        'driver' => null,
        'tickets' => null
    ]);

    return back();
});

// SAVE TICKETS
Route::post('/request/{id}/tickets', function ($id, Request $request) {

    $req = RequestModel::findOrFail($id);

    $req->update([
        'tickets' => $request->tickets
    ]);

    return back();
});

// DELETE (ADMIN)
Route::delete('/request/{id}', function ($id) {

    abort_if(Auth::user()->role !== 'admin', 403);

    RequestModel::findOrFail($id)->delete();

    return back();
});


/*
|--------------------------------------------------------------------------
| DRIVER
|--------------------------------------------------------------------------
*/

// ADD DRIVER
Route::post('/driver', function (Request $request) {

    Driver::create([
        'name' => $request->name,
        'license_no' => $request->license_no,
        'status' => 'Available'
    ]);

    return back();
});

// UPDATE DRIVER
Route::put('/driver/{id}', function ($id, Request $request) {

    abort_if(Auth::user()->role !== 'admin', 403);

    Driver::findOrFail($id)->update([
        'name' => $request->name,
        'license_no' => $request->license_no,
    ]);

    return back();
});

Route::get('/test', function () {
    return 'OK';
});

Route::put('/request/{id}/remarks', [RequestController::class, 'updateRemarks']);
Route::put('/request/{id}/update-all', [RequestController::class, 'updateAll']);
Route::post('/request/{id}/approve', [RequestController::class, 'approve']);
Route::post('/request/{id}/reject', [RequestController::class, 'reject']);
Route::put('/request/{id}/reset-ticket', [RequestController::class, 'resetTicket']);
Route::delete('/request/{id}', [RequestController::class, 'destroy']);
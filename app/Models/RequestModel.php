<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // ✅ ADD THIS

class RequestModel extends Model
{
    protected $table = 'requests';

    protected $fillable = [
        'user_id',
        'passenger',
        'destination',
        'purpose',
        'date',
        'status',
        'driver',
        'tickets',
        'admin_remarks'
    ];

    // 🔥 ADD THIS FUNCTION
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driverData()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'driver');
    }

    public function driverRelation()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'driver');
    }

    
}
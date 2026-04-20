<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
   protected $fillable = [
        'name',
        'license_no',
        'status'
    ];

    public function isBusyOn($date)
    {
        return $this->requests()
            ->where('date', $date)
            ->where('status', 'Approved')
            ->exists();
    }

    public function requests()
    {
        return $this->hasMany(\App\Models\RequestModel::class, 'driver');
    }

    
}

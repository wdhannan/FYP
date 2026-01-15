<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment';
    protected $primaryKey = 'AppointmentID';
    public $timestamps = true;

    protected $fillable = [
        'AppointmentID',
        'ChildID',
        'DoctorID',
        'NurseID',
        'date',
        'time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        // Don't cast time to datetime - keep it as string (e.g., "8:00 AM")
    ];
}


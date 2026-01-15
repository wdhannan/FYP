<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedule';
    protected $primaryKey = 'ScheduleID';
    public $timestamps = true;

    protected $fillable = [
        'ScheduleID',
        'DoctorID',
        'UploadDate',
        'FileName',
    ];

    protected $casts = [
        'UploadDate' => 'date',
    ];
}

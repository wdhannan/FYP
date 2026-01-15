<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';
    protected $primaryKey = 'ReportID';
    public $timestamps = true;

    protected $fillable = [
        'ReportID',
        'ChildID',
        'DoctorID',
        'ReportDate',
        'Diagnosis',
        'Symptoms',
        'Findings',
        'FollowUpAdvices',
        'Notes',
    ];

    protected $casts = [
        'ReportDate' => 'date',
    ];
}

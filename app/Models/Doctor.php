<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctor';
    protected $primaryKey = 'DoctorID';
    public $timestamps = true;

    protected $fillable = [
        'DoctorID',
        'FullName',
        'Email',
    ];
}


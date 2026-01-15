<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    use HasFactory;

    protected $table = 'nurse';
    protected $primaryKey = 'NurseID';
    public $timestamps = true;

    protected $fillable = [
        'NurseID',
        'FullName',
        'Email',
    ];
}


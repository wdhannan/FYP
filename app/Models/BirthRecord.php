<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthRecord extends Model
{
    use HasFactory;

    protected $table = 'birthrecord';
    protected $primaryKey = 'BirthID';
    public $timestamps = true;

    protected $fillable = [
        'BirthID',
        'ChildID',
        'TimeOfBirth',
        'GestationalAgeWeeks',
        'BirthPlace',
        'BirthType',
        'Complications',
        'BabyCount',
        'BirthWeight',
        'BirthLength',
        'BirthCircumference',
        'VitaminKGiven',
        'ApgarScore',
        'BloodGroup',
    ];

    protected $casts = [
        'TimeOfBirth' => 'datetime',
        'GestationalAgeWeeks' => 'integer',
        'BabyCount' => 'integer',
        'BirthWeight' => 'decimal:2',
        'BirthLength' => 'decimal:2',
        'BirthCircumference' => 'decimal:2',
    ];
}

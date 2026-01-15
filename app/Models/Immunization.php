<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Immunization extends Model
{
    use HasFactory;

    protected $table = 'immunization';
    protected $primaryKey = 'ImmunizationID';
    public $timestamps = true;

    protected $fillable = [
        'ImmunizationID',
        'ChildID',
        'Age',
        'VaccineName',
        'Date',
        'DoseNumber',
        'GivenBy',
    ];

    protected $casts = [
        'Date' => 'date',
        'Age' => 'integer',
    ];
}

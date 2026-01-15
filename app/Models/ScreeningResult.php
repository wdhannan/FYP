<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreeningResult extends Model
{
    use HasFactory;

    protected $table = 'screeningresult';
    protected $primaryKey = 'ScreeningID';
    public $timestamps = true;

    protected $fillable = [
        'ScreeningID',
        'ChildID',
        'ScreeningType',
        'Result',
        'DateScreened',
    ];

    protected $casts = [
        'DateScreened' => 'date',
    ];
}

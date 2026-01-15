<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrowthChart extends Model
{
    use HasFactory;

    protected $table = 'growthchart';
    protected $primaryKey = 'GrowthID';
    public $timestamps = true;

    protected $fillable = [
        'GrowthID',
        'ChildID',
        'DateMeasured',
        'Age',
        'Weight',
        'Height',
        'HeadCircumference',
    ];

    protected $casts = [
        'DateMeasured' => 'date',
        'Weight' => 'float',
        'Height' => 'float',
        'HeadCircumference' => 'float',
    ];
}

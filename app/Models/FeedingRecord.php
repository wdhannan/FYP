<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedingRecord extends Model
{
    use HasFactory;

    protected $table = 'feedingrecord';
    protected $primaryKey = 'FeedingID';
    public $timestamps = true;

    protected $fillable = [
        'FeedingID',
        'ChildID',
        'FeedingType',
        'FrequencyPerDay',
        'DateLogged',
        'Remarks',
    ];

    protected $casts = [
        'DateLogged' => 'date',
        'FrequencyPerDay' => 'integer',
    ];
}

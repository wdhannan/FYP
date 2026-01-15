<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentMilestone extends Model
{
    use HasFactory;

    protected $table = 'developmentmilestone';
    protected $primaryKey = 'MilestoneID';
    public $timestamps = true;

    protected $fillable = [
        'MilestoneID',
        'ChildID',
        'MilestoneType',
        'Notes',
    ];
}

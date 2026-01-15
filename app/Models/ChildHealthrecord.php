<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildHealthrecord extends Model
{
    use HasFactory;

    // This model might combine data from multiple tables
    // Adjust based on your actual health record table structure
    protected $table = 'child';
    protected $primaryKey = 'ChildID';
    public $timestamps = false;
}


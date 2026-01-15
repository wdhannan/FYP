<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    // This model might be related to child registration
    // Adjust table name and fields based on your actual registration table
    protected $table = 'child';
    protected $primaryKey = 'ChildID';
    public $timestamps = false;
}


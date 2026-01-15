<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $table = 'child';
    protected $primaryKey = 'ChildID';
    public $timestamps = true;

    protected $fillable = [
        'ChildID',
        'FullName',
        'DateOfBirth',
        'Gender',
        'MyKidNumber',
        'Ethnic',
        'ParentID',
    ];

    protected $casts = [
        'DateOfBirth' => 'date',
    ];
}


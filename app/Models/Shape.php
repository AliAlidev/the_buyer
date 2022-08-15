<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shape extends Model
{
    use HasFactory;
    protected $table = "chapes";
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];
}

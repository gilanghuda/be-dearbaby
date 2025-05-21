<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gejala extends Model
{
    /** @use HasFactory<\Database\Factories\GejalaFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'level',
    ];
}

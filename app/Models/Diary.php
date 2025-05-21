<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
    /** @use HasFactory<\Database\Factories\DiaryFactory> */
    use HasFactory;

    protected $fillable = [
        'message',
        'moodcheck',
        'user_id',
        'created_at',
    ];

    public $timestamps = false; // Karena hanya pakai created_at saja
}

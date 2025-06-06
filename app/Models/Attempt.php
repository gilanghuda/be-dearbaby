<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'submitted_at',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'attempt_id', 'id');
    }
}

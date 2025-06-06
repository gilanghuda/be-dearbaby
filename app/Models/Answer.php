<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'attempt_id',
        'question_id',
        'selected_option_id',
        'is_correct',
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempt::class, 'attempt_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(Option::class, 'selected_option_id', 'id');
    }
}

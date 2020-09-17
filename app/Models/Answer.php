<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'answer';

    protected $casts = [
        'is_correct'   => 'bool',
        'submitted_at' => 'datetime',
        'game_id'      => 'int',
        'question_id'  => 'int',
        'user_id'      => 'int',
    ];

    protected $fillable = [
        'result',
        'answer',
        'question_id',
        'game_id',
        'user_id',
        'submitted_at',
        'created_at',
    ];

    protected $appends = [
        'result',
        'time_limit',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getTimeLimitAttribute()
    {
        return $this->created_at->addSeconds(15);
    }


    public function getIsLateAttribute()
    {
        if ($this->submitted_at === null) {
            return false;
        }

        return $this->submitted_at->getTimestamp() > $this->time_limit->getTimestamp();
    }

    public function getResultAttribute()
    {
        if ($this->getIsLateAttribute()) {
            return 'LATE';
        }

        return $this->answer === $this->question->answer ? 'CORRECT' : 'WRONG';
    }

}

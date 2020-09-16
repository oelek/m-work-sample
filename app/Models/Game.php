<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $table = 'game';

    protected $fillable = [
        'quiz_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questions()
    {
        return $this->hasManyThrough(Question::class, Quiz::class)
                    ->orderBy('order');
    }

    public function getFirstQuestionAttribute()
    {
        return $this->questions()->first();
    }
}

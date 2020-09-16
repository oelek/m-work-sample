<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'question';

    protected $fillable = [
        'text',
        'image_id',
        'options',
        'answer',
        'quiz_id',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function nextQuestion()
    {
        return $this->hasOneThrough(__CLASS__, Quiz::class, 'id', 'quiz_id', 'quiz_id')
                    ->where('question.id', '!=', $this->id)
                    ->orderBy('question.order')
                    ->where('order', '>', $this->order);
    }

}

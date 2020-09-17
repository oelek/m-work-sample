<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $gameId          = $request->route('game');
        $nextQuestionId  = $this->next_question !== null ? $this->next_question->id : null;
        $nextQuestionUrl = $nextQuestionId ? '/api/games/' . $gameId . '/questions/' . $nextQuestionId : null;
        $gameUrl         = '/api/games/' . $gameId;

        return [
            'data' => [
                'type'       => 'question',
                'attributes' => [
                    'id'        => $this->id,
                    'text'      => $this->text,
                    'image_url' => $this->image_url,
                    'options'   => $this->options,
                    'quiz_id'   => $this->quiz_id,
                    'order'     => $this->order,
                ],
            ],
            'meta' => [
                'next_url' => $nextQuestionUrl,
                'next_id'  => $nextQuestionId,
                'game_url' => $gameUrl,
            ],
        ];
    }
}

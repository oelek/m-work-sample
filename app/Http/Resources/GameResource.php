<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
        return [
            'data' => [
                'type'       => 'game',
                'attributes' => [
                    'id'      => $this->id,
                    'quiz_id' => $this->quiz_id,
                    'user_id' => $this->user_id,
                ],
            ],
            'meta' => [
                'question_uri' => '/api/games/' . $this->id . '/questions/' . $this->first_question->id ?? null,
                'question_id'  => $this->first_question->id
            ],
        ];
    }
}

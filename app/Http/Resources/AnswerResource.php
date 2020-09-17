<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
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
            'type'       => 'answer',
            'attributes' => [
                'id'           => $this->id,
                'result'       => $this->result,
                'answer'       => $this->answer,
                'question_id'  => $this->question_id,
                'game_id'      => $this->game_id,
                'submitted_at' => $this->submitted_at,
                'time_limit'   => $this->time_limit,
            ],
        ];
    }
}

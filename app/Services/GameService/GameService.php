<?php

namespace App\Services\GameService;

use App\Models\Game;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GameService
{

    /**
     * @var User
     */
    private $user;

    public function user(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function createGame(string $quizId): Game
    {
        return Game::create([
            'quiz_id' => $quizId,
            'user_id' => $this->user->id,
        ]);
    }

    public function getQuestion(string $gameId, string $questionId): Question
    {
        return $this->getAnswerModelForQuestion($gameId, $questionId)->question;
    }

    public function answerQuestion(string $gameId, string $questionId, string $answer): Answer
    {
        $answerModel               = $this->getAnswerModelForQuestion($gameId, $questionId);
        $answerModel->answer       = $answer;
        $answerModel->submitted_at = now();
        $answerModel->save();

        return $answerModel;
    }

    public function getQuestionWithLifeline(string $gameId, string $questionId, string $type): Question
    {
        return LifeLine::for($this->getAnswerModelForQuestion($gameId, $questionId))->apply($type);
    }

    /**
     * Get answer model for question
     * Makes sure there is only ever one for a given game, user and question
     *
     * @param string $gameId
     * @param string $questionId
     *
     * @return Answer
     */
    private function getAnswerModelForQuestion(string $gameId, string $questionId): Answer
    {
        return Answer::firstOrCreate([
            'game_id'     => $gameId,
            'question_id' => $questionId,
            'user_id'     => $this->user->id,
        ]);
    }

    public function getGameResult($gameId): Collection
    {
        return Answer::where('game_id', $gameId)
                     ->where('user_id', $this->user->id)
                     ->get();
    }

}

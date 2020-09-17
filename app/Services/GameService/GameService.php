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


    /**
     * Set currently playing user
     *
     * @param User $user
     *
     * @return $this
     */
    public function user(User $user): GameService
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Create and start a game
     *
     * @param string $quizId
     *
     * @return Game
     */
    public function createGame(string $quizId): Game
    {
        return Game::create([
            'quiz_id' => $quizId,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Start playing question
     * Will record a time limit
     *
     * @param string $gameId
     * @param string $questionId
     *
     * @return Question
     */
    public function getQuestion(string $gameId, string $questionId): Question
    {
        return $this->getAnswerModelForQuestion($gameId, $questionId)->question;
    }

    /**
     * Submit an answer
     *
     * @param string $gameId
     * @param string $questionId
     * @param string $answer
     *
     * @return Answer
     */
    public function recordAnswer(string $gameId, string $questionId, string $answer): Answer
    {
        $answerModel               = $this->getAnswerModelForQuestion($gameId, $questionId);
        $answerModel->answer       = $answer;
        $answerModel->submitted_at = now();
        $answerModel->save();

        return $answerModel;
    }


    /**
     * Get result for all answered questions
     *
     * @param $gameId
     *
     * @return Collection
     */
    public function getGameResult($gameId): Collection
    {
        return Answer::where('game_id', $gameId)
                     ->where('user_id', $this->user->id)
                     ->get();
    }

    /**
     * Get question, but modified by a lifeline
     * Will also record a time limit if not yet done
     *
     * @param string $gameId
     * @param string $questionId
     * @param string $type
     *
     * @return Question
     */
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
        return $this->question($gameId, $questionId)
                    ->answers()
                    ->firstOrCreate([
                        'game_id'     => $gameId,
                        'question_id' => $questionId,
                        'user_id'     => $this->user->id,
                    ]);
    }

    /**
     * @param string $gameId
     * @param string $questionId
     *
     * @return Question
     */
    private function question(string $gameId, string $questionId): Question
    {
        return Game::findOrFail($gameId)->questions()->findOrFail($questionId);
    }
}

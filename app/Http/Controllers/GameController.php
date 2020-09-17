<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnswerResource;
use App\Http\Resources\GameResource;
use App\Http\Resources\QuestionResource;
use App\Services\GameService\GameService;
use Illuminate\Http\Request;

class GameController extends Controller
{

    /**
     * @var GameService
     */
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'quiz_id' => 'exists:quiz,id'
        ]);

        $game = $this->gameService
            ->user($request->user())
            ->createGame($request->input('quiz_id'));

        return new GameResource($game);
    }

    public function showQuestion(Request $request, $gameId, $questionId)
    {
        $this->gameService->user($request->user());
        if (($type = $request->input('lifeline')) !== null) {
            $question = $this->gameService->getQuestionWithLifeline($gameId, $questionId, $type);

            return new QuestionResource($question);
        }

        $question = $this->gameService->getQuestion($gameId, $questionId);

        return new QuestionResource($question);
    }

    public function storeAnswer(Request $request, $gameId, $questionId)
    {
        $this->validate($request, [
            'answer' => 'required|string'
        ]);

        $answer = $this->gameService
            ->user($request->user())
            ->answerQuestion($gameId, $questionId, $request->input('answer') ?? '');

        return new AnswerResource($answer);
    }

    public function showAnswers(Request $request, $gameId)
    {
        $answers = $this->gameService
            ->user($request->user())->getGameResult($gameId);

        return AnswerResource::collection($answers);
    }
}

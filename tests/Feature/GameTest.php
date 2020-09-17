<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Game;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Foundation\Auth\User;
use Tests\TestCase;

class GameTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateGame()
    {
        $user = User::inRandomOrder()->first();
        $quiz = Quiz::factory()
                    ->has(Question::factory()->count(10))->create();

        $data = ['quiz_id' => $quiz->id,];

        $response = $this->post('/api/games', $data, $this->getAuthHeader($user));
        $question = $quiz->questions->first();

        $response->assertJson([
            'data' => [
                'type'       => 'game',
                'attributes' => [
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id
                ],
            ],
            'meta' => [
                'question_id' => $question->id
            ],
        ]);
    }

    public function testGetQuestionsGame()
    {
        $user = User::inRandomOrder()->first();
        $quiz = Quiz::factory()
                    ->has(Question::factory()->count(10))->create();

        $attributes = [
            'quiz_id' => $quiz->id,
            'user_id' => $user->id
        ];

        $game = Game::factory()->create($attributes);

        foreach ($game->questions as $question) {
            $response = $this->get('/api/games/' . $game->id . '/questions/' . $question->id,
                $this->getAuthHeader($user));

            $response->assertJson([
                'data' => [
                    'type'       => 'question',
                    'attributes' => [
                        'quiz_id' => $quiz->id
                    ],
                ],
            ]);
        }
    }

    public function testAnswerQuestions()
    {
        $user = User::inRandomOrder()->first();
        $quiz = Quiz::factory()
                    ->has(Question::factory()->count(10))->create();

        $attributes = [
            'quiz_id' => $quiz->id,
            'user_id' => $user->id
        ];

        $game = Game::factory()->create($attributes);

        foreach ($game->questions as $question) {
            $randomAnswer = $question->options[array_rand($question->options)];

            $answer = [
                'answer' => $randomAnswer,
            ];

            $response = $this->post('/api/games/' . $game->id . '/questions/' . $question->id, $answer,
                $this->getAuthHeader($user));

            $result = $randomAnswer === $question->answer ? 'CORRECT' : 'WRONG';

            $response->assertJson([
                'data' => [
                    'type'       => 'answer',
                    'attributes' => [
                        'game_id'     => $game->id,
                        'question_id' => $question->id,
                        'result'      => $result,
                        'answer'      => $randomAnswer,
                    ],
                ],
            ]);
        }
    }

    public function testLateSubmissionShouldFail()
    {
        $user = User::inRandomOrder()->first();
        $quiz = Quiz::factory()
                    ->has(Question::factory()->count(10))->create();

        $attributes = [
            'quiz_id' => $quiz->id,
            'user_id' => $user->id
        ];

        $game = Game::factory()->create($attributes);

        foreach ($game->questions as $question) {

            $fakeDate = now()->addSeconds(-50);

            Answer::firstOrCreate([
                'game_id'     => $game->id,
                'question_id' => $question->id,
                'user_id'     => $user->id,
                'created_at'  => $fakeDate,
                'timestamps'  => false,
            ]);

            $answer = [
                'answer' => $question->answer,
            ];

            $response = $this->post('/api/games/' . $game->id . '/questions/' . $question->id, $answer,
                $this->getAuthHeader($user));

            $response->assertJson([
                'data' => [
                    'type'       => 'answer',
                    'attributes' => [
                        'game_id'     => $game->id,
                        'question_id' => $question->id,
                        'result'      => 'LATE',
                        'answer'      => $question->answer,
                    ],
                ],
            ]);
        }
    }

    public function testDelayDeadline()
    {
        $user = User::inRandomOrder()->first();
        $quiz = Quiz::factory()
                    ->has(Question::factory()->count(10))->create();

        $attributes = [
            'quiz_id' => $quiz->id,
            'user_id' => $user->id
        ];

        $game     = Game::factory()->create($attributes);
        $question = $game->questions->first();

        $answer = Answer::firstOrCreate([
            'game_id'     => $game->id,
            'question_id' => $question->id,
            'user_id'     => $user->id,
        ]);

        $answer->created_at = $answer->created_at->addSeconds(-16);
        $answer->save(['timestamps' => false]);


        $this->get('/api/games/' . $game->id . '/questions/' . $question->id . '?lifeline=buytime',
            $this->getAuthHeader($user));

        $answer = ['answer' => 'hello'];

        $response = $this->post('/api/games/' . $game->id . '/questions/' . $question->id, $answer,
            $this->getAuthHeader($user));

        $response->assertJson([
            'data' => [
                'type'       => 'answer',
                'attributes' => [
                    'result'      => 'WRONG',
                    'question_id' => $question->id,
                ],
            ],
        ]);
    }

    public function testFiftyFifty()
    {
        $user       = User::inRandomOrder()->first();
        $quiz       = Quiz::factory()
                          ->has(Question::factory()->count(10))->create();
        $attributes = [
            'quiz_id' => $quiz->id,
            'user_id' => $user->id
        ];

        $game     = Game::factory()->create($attributes);
        $question = $game->questions->first();

        $response = $this->get('/api/games/' . $game->id . '/questions/' . $question->id . '?lifeline=5050',
            $this->getAuthHeader($user));

        $content = json_decode($response->getContent());

        $this->assertCount(2, $content->data->attributes->options);
        $this->assertTrue(in_array($question->answer, $content->data->attributes->options));
    }
}

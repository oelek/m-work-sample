<?php

namespace Tests\Unit;

use App\Models\Answer;
use App\Models\Question;
use App\Services\GameService\LifeLine;
use Tests\TestCase;

class LifeLineTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testRemoveOptions()
    {
        $answer = Answer::factory()->make();
        LifeLine::for($answer)->removeIncorrectOptions();

        $this->assertCount(2, $answer->question->options);
        $this->assertTrue(in_array($answer->question->answer, $answer->question->options));
    }

    public function testDelayDeadline()
    {
        $answer          = Answer::factory()->make();
        $delayedDeadline = now()->addSeconds(15 + 10);

        LifeLine::for($answer)->delayDeadline();
        $this->assertEquals($delayedDeadline->getTimestamp(), $answer->time_limit->getTimestamp());
        $this->assertEquals($delayedDeadline->getTimestamp(), Answer::find($answer->id)->time_limit->getTimestamp());

    }
}

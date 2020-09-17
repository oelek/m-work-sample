<?php

namespace App\Services\GameService;

use App\Models\Answer;
use App\Models\Question;

class LifeLine
{

    const FIFTYFITFY = '5050';

    const DELAY = 'buytime';

    /**
     * @var Answer
     */
    private $answer;

    /**
     * LifeLine constructor.
     *
     * @param Answer $answer
     */
    public function __construct(Answer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * Factory method
     *
     * @param Answer $answer
     *
     * @return LifeLine
     */
    public static function for(Answer $answer): LifeLine
    {
        return new self($answer);
    }

    /**
     * Apply lifeline to answer and or question
     *
     * @param string $type
     *
     * @return mixed
     */
    public function apply(string $type): Question
    {
        if ($type === self::DELAY) {
            $this->delayDeadline();
        }

        if ($type === self::FIFTYFITFY) {
            $this->removeIncorrectOptions();
        }

        return $this->answer->question;
    }

    /**
     * Removes half of incorrect options in question
     */
    public function removeIncorrectOptions()
    {
        $question = $this->answer->question;

        $dontRemove = $question->answer;

        $options = array_filter($question->options, function ($option) use ($dontRemove) {
            return $option !== $dontRemove;
        });

        $option          = $options[array_rand($options)];
        $filteredOptions = [$option, $dontRemove];
        shuffle($filteredOptions);
        $question->options = $filteredOptions;
    }

    /**
     * Delays time limit by 10s
     */
    public function delayDeadline()
    {
        $this->answer->created_at = $this->answer->created_at->addSeconds(10);
        $this->answer->save(['timestamps' => false]);
    }

}

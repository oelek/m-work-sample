<?php

namespace App\Services\GameService;

use App\Models\Answer;

class LifeLine
{

    const FIFTYFITFY = '5050';

    const DELAY = 'buytime';

    /**
     * @var Answer
     */
    private $answer;

    public function __construct(Answer $answer)
    {
        $this->answer = $answer;
    }

    public static function for(Answer $answer)
    {
        return new self($answer);
    }

    public function apply(string $type)
    {
        if ($type === self::DELAY) {
            $this->delayDeadline();
        }

        if ($type === self::FIFTYFITFY) {
            $this->removeIncorrectOptions();
        }

        return $this->answer->question;
    }

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

    public function delayDeadline()
    {
        $this->answer->created_at = $this->answer->created_at->addSeconds(10);
        $this->answer->save(['timestamps' => false]);
    }

}

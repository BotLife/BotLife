<?php

namespace Botlife\Command;

class EightBall extends ACommand
{

    public $regex  = '/^[.!@]8(ball)?( (?P<question>.*))?$/i';
    public $action = 'run';
    
    public $answers = array(
        'Yes!',
        'No....',
        'Maybe..',
        'You wish',
    );
    
    public function run($event)
    {
        $c = new \Botlife\Application\Colors;
        
        if (!isset($event->matches['question'])) {
            \Ircbot\Notice(
                $event->mask->nickname,
                $c(12, '[') . $c(3, '8BALL') . $c(12, '] ')
                    . $c(12, 'You need to specify a question. For example: ')
                    . $c(3, '!8ball Should a buy a rune chestplate?')
            );  
            return;
        }
        $question = $event->matches['question'];
        $answer   = $this->getAnswer();
        
        \Ircbot\Notice(
            $event->mask->nickname,
            $c(12, '[') . $c(3, '8BALL') . $c(12, '] ')
                . $c(12, 'Question: ') . $c(3, $question) . $c(12, ' - ')
                . $c(12, 'Answer: ') . $c(3, $answer)
        );   
    }
    
    public function getAnswer()
    {
        return $this->answers[array_rand($this->answers)];
    }

}

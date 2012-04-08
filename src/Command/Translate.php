<?php

namespace Botlife\Command;

class Translate extends ACommand
{

    public $regex = array(
        '/^[.@!]translate(?P<langs> (?P<from>[a-zA-Z]{2})(\|| )(?P<to>[a-zA-Z]{2}))?( (?P<text>.*))?$/i',
    );
    public $action = 'translate';
    public $code   = 'translate';
    
    public $languages = array('nl', 'en');

    public function translate($event)
    {
        $this->detectResponseType($event->message, $event->target);
        $c = new \Botlife\Application\Colors;
        if (!isset($event->matches['langs'])) {
            $this->respondWithPrefix(
                $c(12, 'You need to specify two languages. For example: ')
                    . $c(3, '!translate nl en Hoe gaat het?')
            );  
            return;
        }
        if (!isset($event->matches['text'])) {
            $this->respondWithPrefix(
                $c(12, 'You need to specify a text. For example: ')
                    . $c(3, '!translate nl en Hoe gaat het?')
            );  
            return;
        }
        if (!in_array(strtolower($event->matches['from']), $this->languages)) {
            $this->respondWithPrefix(
                'The language you\'re trying to translate from isn\'t supported'
            );
            return;
        }
        if (!in_array(strtolower($event->matches['to']), $this->languages)) {
            $this->respondWithPrefix(
                'The language you\'re trying to translate to isn\'t supported'
            );
            return;
        }
        $response = $this->getTranslation(
            $event->matches['from'], $event->matches['to'],
            $event->matches['text']
        );
        if (!$response) {
            $this->respondWithPrefix('Could not translate your text');
            return;
        }
        $msg = $response;
        $this->respondWithPrefix($msg);
    }
    
    public function getTranslation($from, $to, $text)
    {
        $url = 'http://mymemory.translated.net/api/get';
        $url .= '?q=' . urlencode($text);
        $url .= '&langpair=' . $from . '|' . $to;
        $data = json_decode(file_get_contents($url));
        return $data->responseData->translatedText;
    }
    
}

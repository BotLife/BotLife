<?php

namespace Botlife\Command;

class Translate extends ACommand
{

    public $regex = array(
        '/^[.@!]translate(?P<langs> (?P<from>[a-zA-Z]{2})(\|| )(?P<to>[a-zA-Z]{2}))?( (?P<text>.*))?$/i',
    );
    public $action = 'translate';
    
    public $languages = array('nl', 'en');

    public function translate($event)
    {
        $this->detectResponseType($event->message);
        $c = new \Botlife\Application\Colors;
        if (!isset($event->matches['langs'])) {
            $this->respond(
                $c(12, '[') . $c(3, 'TRANS') . $c(12, '] ')
                    . $c(12, 'You need to specify two languages. For example: ')
                    . $c(3, '!translate nl en Hoe gaat het?')
            );  
            return;
        }
        if (!isset($event->matches['text'])) {
            $this->respond(
                $c(12, '[') . $c(3, 'TRANS') . $c(12, '] ')
                    . $c(12, 'You need to specify a text. For example: ')
                    . $c(3, '!translate nl en Hoe gaat het?')
            );  
            return;
        }
        if (!in_array(strtolower($event->matches['from']), $this->languages)) {
            $msg = 'The language you\'re trying to translate from isn\'t supported';
            $this->respond(
                $this->styleMessage($msg)
            );
            return;
        }
        if (!in_array(strtolower($event->matches['to']), $this->languages)) {
            $msg = 'The language you\'re trying to translate to isn\'t supported';
            $this->respond(
                $this->styleMessage($msg)
            );
            return;
        }
        $response = $this->getTranslation(
            $event->matches['from'], $event->matches['to'],
            $event->matches['text']
        );
        if (!$response) {
            $msg = 'Could not translate your text';
            $this->respond(
                $this->styleMessage($msg)
            );
            return;
        }
        $msg = $response;
        $this->respond($this->styleMessage($msg));
    }
    
    public function styleMessage($text)
    {
        $C = new \Botlife\Application\Colors;
        return $C(12, '[') . $C(3, 'MISC') . $C(12, '][') . $C(3, 'TRANSLATE')
            . $C(12, ']') . $C(12, ' ' . $text);
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

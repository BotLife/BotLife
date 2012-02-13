<?php

namespace Botlife\Command;

class Translate
{

    public $regex = array(
        '/^[.@!]translate (?P<from>[a-zA-Z]{2})(\|| )(?P<to>[a-zA-Z]{2}) (?P<text>.*)$/i',
    );
    public $action = 'translate';
    
    public $languages = array('nl', 'en');

    public function translate($event)
    {
        if (!in_array(strtolower($event->matches['from']), $this->languages)) {
            $msg = 'The language you\'re trying to translate from isn\'t supported';
            \Ircbot\notice(
                $event->mask->nickname,
                $this->styleMessage($msg)
            );
            return;
        }
        if (!in_array(strtolower($event->matches['to']), $this->languages)) {
            $msg = 'The language you\'re trying to translate to isn\'t supported';
            \Ircbot\notice(
                $event->mask->nickname,
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
            \Ircbot\notice(
                $event->mask->nickname,
                $this->styleMessage($msg)
            );
            return;
        }
        $msg = $response;
        \Ircbot\notice($event->mask->nickname, $this->styleMessage($msg));
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

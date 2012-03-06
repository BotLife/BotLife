<?php

namespace Botlife;

class Debug extends \Ircbot\Application\Debug\ADebug
{

    protected $_longestSign = 54;

    public function log($category, $type, $message,
        $level = IRCBOT_DEBUG_NORMAL) {

        $signLen = strlen('[' . $category . '|' . $type . ']');
        if ($signLen > $this->_longestSign) {
            $this->_longestSign = $signLen;
        }
        $c = new Application\Colors;
        $c->output = Application\Colors::OUTPUT_ANSI;
        $options = getopt('f');
        if (isset($options['f'])) {
            echo str_pad(
                $this->getSign($c, $category, $type), $this->_longestSign
            ) . ' ' . $message . PHP_EOL;
        }
        file_put_contents(
            'botlife/log',
            str_pad('[' . $category . '|' . $type . ']', $this->_longestSign)
                . ' ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }
    
    public function getSign($c, $category, $type)
    {
        return $c($c::STYLE_BOLD) . $c(12) . '[' . $c(3, $category) . $c(12) . '|'
            . $c(3, $type) . $c(12) . ']' . $c(12);
    }

}

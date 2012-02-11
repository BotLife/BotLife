<?php

namespace BotLife\Console;

class Colors
{
    const COLOR_NORMAL = -1;
    const COLOR_WHITE  = 0;
    const COLOR_BLACK  = 1;
    const COLOR_BLUE   = 2;
    const COLOR_GREEN  = 3;
    const COLOR_RED    = 4;
    
    private $_ansi = array(
        self::COLOR_WHITE   => '[0;37m',
        self::COLOR_BLACK   => '[0;30m',
        self::COLOR_BLUE    => '[0;34m',
        self::COLOR_GREEN   => '[0;32m',
        self::COLOR_RED     => '[0;31m',
        self::COLOR_NORMAL  => '[0m',
    );
    
    public function __invoke($color = -1, $text = null)
    {
        if ($text) {
            return self::__invoke($color) . $text;
        } else {
            return chr(27) . $this->_ansi[$color];
        }
    }
    
}

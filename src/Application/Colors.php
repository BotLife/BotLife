<?php

namespace Botlife\Application;

class Colors
{
    const COLOR_WHITE         = 0;
    const COLOR_BLACK         = 1;
    const COLOR_BLUE          = 2;
    const COLOR_GREEN         = 3;
    const COLOR_RED           = 4;
    const COLOR_BROWN         = 5;
    const COLOR_PURPLE        = 6;
    const COLOR_ORANGE        = 7;
    const COLOR_YELLOW        = 8;
    const COLOR_LIGHT_GREEN   = 9;
    const COLOR_CYAN          = 10;
    const COLOR_LIGHT_CYAN    = 11;
    const COLOR_LICHT_BLUE    = 12;
    const COLOR_PINK          = 13;
    const COLOR_GRAY          = 14;
    const COLOR_LIGHT_GRAY    = 15;
    
    const STYLE_NORMAL        = -1;
    const STYLE_BOLD          = -2;
    const STYLE_UNDERLINE     = -3;
    const STYLE_REVERSE       = -4;
    const STYLE_STRIKETHROUGH = -5;
    const STYLE_ITALIC        = -6;
    
    const OUTPUT_IRC          = 1;
    const OUTPUT_ANSI         = 2;
    
    private $_ansi = array(
        self::COLOR_WHITE         => '[37m',
        self::COLOR_BLACK         => '[30m',
        self::COLOR_BLUE          => '[34m',
        self::COLOR_GREEN         => '[32m',
        self::COLOR_RED           => '[31m',
        self::COLOR_BROWN         => '[33m',
        self::COLOR_PURPLE        => '[35m',
        self::COLOR_ORANGE        => '[0m',
        self::COLOR_YELLOW        => '[33m',
        self::COLOR_LIGHT_GREEN   => '[32m',
        self::COLOR_CYAN          => '[36m',
        self::COLOR_LIGHT_CYAN    => '[36m',
        self::COLOR_LICHT_BLUE    => '[34m',
        self::COLOR_PINK          => '[0m',
        self::COLOR_GRAY          => '[0m',
        self::COLOR_LIGHT_GRAY    => '[0m',
        
        self::STYLE_NORMAL        => '[0m',
        self::STYLE_BOLD          => '[1m',
        self::STYLE_UNDERLINE     => '[4m',
        self::STYLE_REVERSE       => '[7m',
        self::STYLE_STRIKETHROUGH => '[9m',
        self::STYLE_ITALIC        => '[3m',
        
    );
    
    public $output = self::OUTPUT_IRC;
    
    public function __invoke($color = self::STYLE_NORMAL, $text = null)
    {
        if ($text) {
            return self::__invoke($color) . $text;
        } else {
            if ($this->output == self::OUTPUT_IRC) {
                switch ($color) {
                    case self::STYLE_NORMAL:
                        return chr(15);
                    case self::STYLE_BOLD:
                        return chr(2);
                    case self::STYLE_UNDERLINE:
                        return chr(31);
                    case self::STYLE_REVERSE:
                        return chr(22);
                    case self::STYLE_STRIKETHROUGH:
                        return;
                    case self::STYLE_ITALIC:
                        return chr(29);  
                    default: 
                        return chr(3) . $color;
                }
            } elseif ($this->output == self::OUTPUT_ANSI) {
                if (!isset($this->_ansi[$color])) {
                    return;
                }
                return chr(27) . $this->_ansi[$color];
            }
            
        }
    }
    
}

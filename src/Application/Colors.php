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
    const COLOR_LIGHT_BLUE    = 12;
    const COLOR_PINK          = 13;
    const COLOR_GRAY          = 14;
    const COLOR_LIGHT_GRAY    = 15;
    
    const STYLE_NORMAL        = -1;
    const STYLE_BOLD          = -2;
    const STYLE_UNDERLINE     = -3;
    const STYLE_REVERSE       = -4;
    const STYLE_STRIKETHROUGH = -5;
    const STYLE_ITALIC        = -6;
    const STYLE_NONE          = -7;
    
    const OUTPUT_IRC          = 1;
    const OUTPUT_ANSI         = 2;
    
    private $_ansi = array(
        self::COLOR_WHITE         => '[37m',
        self::COLOR_BLACK         => '[30m',
        self::COLOR_BLUE          => '[34m',
        self::COLOR_GREEN         => '[32m',
        self::COLOR_RED           => '[31m',
        self::COLOR_BROWN         => '[0m',
        self::COLOR_PURPLE        => '[35m',
        self::COLOR_ORANGE        => '[0m',
        self::COLOR_YELLOW        => '[33m',
        self::COLOR_LIGHT_GREEN   => '[32m',
        self::COLOR_CYAN          => '[36m',
        self::COLOR_LIGHT_CYAN    => '[36m',
        self::COLOR_LIGHT_BLUE    => '[34m',
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
    private $_irc = array(
        self::STYLE_NORMAL        => 15,
        self::STYLE_BOLD          => 2,
        self::STYLE_UNDERLINE     => 31,
        self::STYLE_REVERSE       => 22,
        self::STYLE_STRIKETHROUGH => null,
        self::STYLE_ITALIC        => 29, 
    );
    
    public $naming = array(
        'white'       => self::COLOR_WHITE,
        'black'       => self::COLOR_BLACK,
        'blue'        => self::COLOR_BLUE,
        'green'       => self::COLOR_GREEN,
        'red'         => self::COLOR_RED,
        'brown'       => self::COLOR_BROWN,
        'purple'      => self::COLOR_PURPLE,
        'orange'      => self::COLOR_ORANGE,
        'yellow'      => self::COLOR_YELLOW,
        'light green' => self::COLOR_LIGHT_GREEN,
        'cyan'        => self::COLOR_CYAN,
        'light cyan'  => self::COLOR_LIGHT_CYAN,
        'light blue'  => self::COLOR_LIGHT_BLUE,
        'pink'        => self::COLOR_PINK,
        'gray'        => self::COLOR_GRAY,
        'light gray'  => self::COLOR_LIGHT_GRAY,
        'none'        => self::STYLE_NONE,
    );
    
    public $specialMapping = array();
    
    public $output = self::OUTPUT_IRC;
    
    public function __construct()
    {
        $colors = Config::getOption('misc.color');
        foreach ($colors as $index => $color) {
            $color = strtolower($color);
            if (!is_numeric($color)) {
                if (isset($this->naming[$color])) {
                    $color = $this->naming[$color];
                } else {
                    $color = self::STYLE_NONE;
                }
            }
            $this->specialMapping[$index + 1] = (int) $color;
        }
    }
    
    public function __invoke($color = self::STYLE_NORMAL, $text = null)
    {
        if (isset($this->specialMapping[$color - 100])) {
            $color = $this->specialMapping[$color - 100];
        }
        if ($text) {
            return self::__invoke($color) . $text;
        } else {
            if ($color == self::STYLE_NONE) {
                return $text;
            }
            if ($this->output == self::OUTPUT_IRC) {
                if (isset($this->_irc[$color])) {
                    return sprintf('%c', $this->_irc[$color]);
                } else {
                    if ($color < 10) {
                        $color = '0' . $color;
                    }
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

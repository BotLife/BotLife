<?php

namespace Botlife;

use \Botlife\Application\Config;

class Debug extends \Ircbot\Application\Debug\ADebug
{

    public function __construct()
    {
        $all = 0;
        $all |= self::LEVEL_DEBUG;
        $all |= self::LEVEL_INFO;
        $all |= self::LEVEL_WARN;
        $all |= self::LEVEL_ERROR;
        $all |= self::LEVEL_FATAL;
        Config::addOption('debug.level', 'string', (string) $all);
    }

    public function log(
        $category, $type, $message, $level = self::LEVEL_INFO
    ) {
        $logLevel = (int) Config::getOption('debug.level');
        if ($level & $logLevel) {
            $c = new Application\Colors;
            $c->output = Application\Colors::OUTPUT_ANSI;
            echo $level . $this->getSign($c, $category, $type)
                . ' ' . $message . PHP_EOL;
        }
    }
    
    public function getSign($c, $category, $type)
    {
        return $c($c::STYLE_BOLD) . $c(12) . '[' . $c(3, $category) . $c(12) . '|'
            . $c(3, $type) . $c(12) . ']' . $c(12);
    }

}

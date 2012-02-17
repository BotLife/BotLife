<?php

namespace Botlife;

class Debug extends \Ircbot\Application\Debug\ADebug
{

    public function log($category, $type, $message,
        $level = IRCBOT_DEBUG_NORMAL) {
        $c = new Application\Colors;
        $c->output = Application\Colors::OUTPUT_ANSI;
        echo $this->getSign($c, $category, $type)
            . ' ' . $message . PHP_EOL;
    }
    
    public function getSign($c, $category, $type)
    {
        return $c($c::STYLE_BOLD) . $c(12) . '[' . $c(3, $category) . $c(12) . '|'
            . $c(3, $type) . $c(12) . ']' . $c(12);
    }

}

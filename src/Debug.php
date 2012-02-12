<?php

namespace Botlife;

class Debug extends \Ircbot\Application\Debug\ADebug
{

    public function log($category, $type, $message,
        $level = IRCBOT_DEBUG_NORMAL) {
        $c = new Application\Colors;
        $c->output = Application\Colors::OUTPUT_ANSI;
        echo $this->getSign($c, $category, $type)
            . ' ' . $c(1, $message)
            . PHP_EOL;
    }
    
    public function getSign($c, $category, $type)
    {
        return $c(1) . '[' . $c(4, $category) . $c(1) . '|'
            . $c(4, $type) . $c(1) . ']' . $c();
    }

}

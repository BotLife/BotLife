<?php

namespace Botlife;

class Debug extends \IRCBot_Debugger_Abstract
{

    public function log($category, $type, $message,
        $level = IRCBOT_DEBUG_NORMAL) {
        $c = new Application\Colors;
        $c->output = Application\Colors::OUTPUT_CONSOLE;
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

<?php

namespace Botlife;

class Debug extends \IRCBot_Debugger_Abstract
{

    public function log($category, $type, $message,
        $level = IRCBOT_DEBUG_NORMAL) {
        $C = new Console\Colors;
        echo $this->getSign($category, $type)
            . ' ' . $C(1, $message)
            . PHP_EOL;   
    }
    
    public function getSign($category, $type)
    {
        $C = new Console\Colors;
        return $C(1) . '[' . $C(4, $category) . $C(1) . '|'
            . $C(4, $type) . $C(1) . ']' . $C();
    }

}

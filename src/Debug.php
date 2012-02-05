<?php

class BotLife_Debug extends IRCBot_Debugger_Abstract
{

    public function log($category, $type, $message,
        $level = IRCBOT_DEBUG_NORMAL) {
        echo sprintf('[%s|%s] %s', $category, $type, $message) . PHP_EOL;   
    }

}

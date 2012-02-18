<?php

namespace Botlife\Application;

class Spamfilter
{

    static private $_hostFilter = array();

    static public function checkCommand(\Ircbot\Type\MessageCommand $command)
    {
        $host = strtolower($command->mask->host);
        if (isset(self::$_hostFilter[$host])) {
            ++self::$_hostFilter[$host];
        } else {
            self::$_hostFilter[$host] = 1;
        }
        if (self::$_hostFilter[$host] >= 3) {
            return false;
        } else {
            return true;
        }
    }
    
    static public function decreaseAmount()
    {
        foreach (self::$_hostFilter as $host => &$commands) {
            --$commands;
            if ($commands == 0) {
                unset(self::$_hostFilter[$host]);
            }
        }
    }

}

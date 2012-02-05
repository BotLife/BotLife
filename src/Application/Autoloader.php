<?php

class BotLife_Application_Autoloader
{

    public function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($class)
    {
        $tmp = explode('_', $class);
        $package = $tmp[0];
        array_shift($tmp);
        if ($package == 'BotLife') {
            require_once implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
        } elseif ($package == 'IRCBot') {
            array_unshift($tmp, 'IRCBot', 'src');
            require_once implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
        }
    }

}

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
        array_shift($tmp);
        require_once implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
        array_unshift($tmp, 'IRCBot', 'src');
        require_once implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
    }

}

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
        require implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
    }

}

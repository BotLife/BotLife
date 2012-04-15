<?php

namespace Botlife\Application;

class Autoloader
{

    public function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($class)
    {
        if (strstr($class, '\\')) {
            $classPath = explode('\\', $class);
            $package = array_shift($classPath);
            if ($package == 'Botlife') {
                require_once implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
            } elseif ($package == 'Ircbot') {
                require_once 'IRCBot/src/' . implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
            } else {
                array_unshift($classPath, $package);
                require_once implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
            }
        } else {
            require_once $class . '.php';
        }
    }

}

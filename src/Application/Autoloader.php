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
            }
        } else {
            $tmp = explode('_', $class);
            $package = $tmp[0];
            array_shift($tmp);
            if ($package == 'IRCBot') {
                array_unshift($tmp, 'IRCBot', 'src');
                require_once implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
            }
        }
    }

}

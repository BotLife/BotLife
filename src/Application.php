<?php

require_once 'Bootstrap.php';
require_once 'Application/Autoloader.php';

class BotLife_Application
{
    
    private $_bootstrap;
    private $_autoloader;
    
    public function __construct()
    {
        $this->_autoloader = new BotLife_Application_Autoloader();
    }
    
    public function getBootstrap()
    {
        if (!$this->_bootstrap) {
            $this->_bootstrap = new BotLife_Bootstrap();
        }
        return $this->_bootstrap;
    }
    
    public function getAutoloader()
    {
        return $this->_autoloader;
    }
    
    public function bootstrap()
    {
        $methods = get_class_methods($this->getBootstrap());
        foreach ($methods as $method) {
            call_user_func(array($this->getBootstrap(), $method));
        }
        return $this;
    }
    
    public function run()
    {
        $this->getBootstrap>run();
    }
    
}

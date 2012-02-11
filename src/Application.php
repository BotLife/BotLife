<?php

namespace BotLife;

class Application
{
    
    private $_bootstrap;
    private $_autoloader;
    
    public function __construct()
    {
        $this->_autoloader = new Application\Autoloader();
    }
    
    public function getBootstrap()
    {
        if (!$this->_bootstrap) {
            $this->_bootstrap = new Bootstrap();
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

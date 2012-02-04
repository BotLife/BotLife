<?php

require_once 'Bootstrap.php';

class BotLife_Application
{
    
    private $_bootstrap;
    
    public function __construct()
    {
    }
    
    public function getBootstrap()
    {
        if (!$this->_bootstrap) {
            $this->_bootstrap = new BotLife_Bootstrap();
        }
        return $this->_bootstrap;
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

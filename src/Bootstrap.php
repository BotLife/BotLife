<?php

class BotLife_Bootstrap
{
    
    public function initModules()
    {
        
    }
    
    public function run()
    {
        IRCBot_Application::getInstance()->getLoop()->startLoop();
    }
    
}

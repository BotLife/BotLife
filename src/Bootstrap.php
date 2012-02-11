<?php

namespace Botlife;

class Bootstrap
{
    
    public function initDebugger()
    {
        \IRCBot_Application::getInstance()->setDebugger(new Debug());
    }
    
    public function initModules()
    {
        new Modules\Main();
    }
    
    public function initBot()
    {
        $bot = new \IRCBot_Types_Bot();
        $bot->nickname = 'BotLife';
        $bot->connect('irc.digital-ground.nl');
        \IRCBot_Application::getInstance()->getBotHandler()->addBot($bot);
    }
    
    public function run()
    {
        \IRCBot_Application::getInstance()->getLoop()->startLoop();
    }
    
}

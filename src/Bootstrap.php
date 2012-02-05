<?php

class BotLife_Bootstrap
{
    
    public function initModules()
    {
        new BotLife_Modules_Main();
    }
    
    public function initBot()
    {
        $bot = new IRCBot_Types_Bot();
        $bot->nickname = 'BotLife';
        $bot->connect('irc.mms-projects.net');
        IRCBot_Application::getInstance()->getBotHandler()->addBot($bot);
    }
    
    public function run()
    {
        IRCBot_Application::getInstance()->getLoop()->startLoop();
    }
    
}

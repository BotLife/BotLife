<?php

namespace Botlife;

use \IRCBot_Types_Bot as Bot;
use \IRCBot_Application as Ircbot;

class Bootstrap
{
    
    public function initDebugger()
    {
        Ircbot::getInstance()->setDebugger(new Debug());
    }
    
    public function initModules()
    {
        new Module\Main();
    }
    
    public function initBot()
    {
        $bot = new Bot();
        $bot->nickname = 'BotLife';
        $bot->connect('irc.digital-ground.nl');
        Ircbot::getInstance()->getBotHandler()->addBot($bot);
    }
    
    public function run()
    {
        Ircbot::getInstance()->getLoop()->startLoop();
    }
    
}

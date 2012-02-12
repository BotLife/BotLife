<?php

namespace Botlife;

use \Ircbot\Type\Bot as Bot;
use \Ircbot\Application as Ircbot;
use \Botlife\Module\Main as MainModule;

class Bootstrap
{
    
    public function initDebugger()
    {
        Ircbot::getInstance()->setDebugger(new Debug());
    }
    
    public function initModules()
    {
        new MainModule;
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

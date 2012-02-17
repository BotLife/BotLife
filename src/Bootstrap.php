<?php

namespace Botlife;

use \Ircbot\Type\Bot as Bot;
use \Ircbot\Application as Ircbot;
use \Botlife\Module\Main as MainModule;
use \Botlife\Module\Misc as MiscModule;
use \Botlife\Module\Math as MathModule;
use \Botlife\Module\Auth as AuthModule;

class Bootstrap
{
    
    public function initDebugger()
    {
        Ircbot::getInstance()->setDebugger(new Debug());
    }
    
    public function initModules()
    {
        new MainModule;
        new MiscModule;
        new MathModule;
        new AuthModule;
    }
    
    public function initBot()
    {
        $bot = new Bot();
        $bot->nickname = 'BotLife';
        $bot->connect('84.28.22.160');
        Ircbot::getInstance()->getBotHandler()->addBot($bot);
    }
    
    public function run()
    {
        Ircbot::getInstance()->getLoop()->startLoop();
    }
    
}

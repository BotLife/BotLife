<?php

namespace Botlife;

use \Ircbot\Type\Bot as Bot;
use \Ircbot\Application as Ircbot;
use \Botlife\Module\Main as MainModule;
use \Botlife\Module\Misc as MiscModule;
use \Botlife\Module\Math as MathModule;
use \Botlife\Module\Auth as AuthModule;
use \Botlife\Module\Admin as AdminModule;

class Bootstrap
{
    
    public function initDebugger()
    {
        Ircbot::getInstance()->setDebugger(new Debug());
        \System_Daemon::setOption(
            'usePEARLogInstance', Ircbot::getInstance()->getDebugger()
        );
    }
    
    public function initDaemon()
    {
        \System_Daemon::setOption('usePEAR'       , false);
        \System_Daemon::setOption('appName'       , 'botlife');
        \System_Daemon::setOption('appDir'        , dirname(__FILE__) . '/..');
        \System_Daemon::setOption(
            'appPidLocation', dirname(__FILE__) . '/../botlife/log.pid'
        );
        \System_Daemon::setOption('appRunAsUID'   , getmyuid()); 
        \System_Daemon::setOption('appRunAsGID'   , getmygid()); 
        \System_Daemon::start();
    }
    
    public function initTimezone()
    {
        date_default_timezone_set('UTC');
    }
    
    public function initModules()
    {
        new MainModule;
        new MiscModule;
        new MathModule;
        new AuthModule;
        new AdminModule;
        new \Botlife\Module\Bar;
        new \Botlife\Module\Invite;
        new \Botlife\Module\Channel;
    }
    
    public function initBot()
    {
        include 'config.php';
        $bot = new Bot();
        $bot->ident = 'BotLife';
        $bot->nickname = 'BotLife';
        $bot->connect('localhost');
        $bot->sendRawData(
            new \Ircbot\Command\Pass($bnc_pass)
        );
        Ircbot::getInstance()->getBotHandler()->addBot($bot);
    }
    
    public function run()
    {
        Ircbot::getInstance()->getLoop()->startLoop();
    }
    
}

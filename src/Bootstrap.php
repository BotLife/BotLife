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
    }
    
    public function initTimezone()
    {
        date_default_timezone_set('UTC');
    }
    
    public function initProxy()
    {
        $debug = new \Botlife\Debug;
        $httpProxy = (getenv('https_proxy') ? getenv('https_proxy')
                      : (getenv('http_proxy')) ? getenv('http_proxy')
                        : getenv('HTTP_PROXY'));
        if (!$httpProxy) {
            return;
        }
        $parts = parse_url($httpProxy);
        if (!$parts) {
            $debug->log(
            	'NET', 'PROXY', 'Malformed proxy given!	'
            );
            return;
        }
        $proxy = 'tcp://' . $parts['host'] . ':' .  $parts['port'];
        $options = stream_context_get_options(stream_context_get_default());
        $options['http']['proxy'] = $proxy;
        $context = stream_context_set_default($options);
        libxml_set_streams_context($context);
        $debug->log(
        	'NET', 'PROXY', 'Set the default HTTP proxy to: ' . $proxy
        );
    }

    public function initGetters()
    {
        $httpRequest = new Entity\HttpRequest;
        \DataGetter::addCallback(
        	'file-content', 'curl-content',
            array($httpRequest, 'doCurl'), 25
        );
        \DataGetter::addCallback(
        	'file-content', 'file-get-content',
            array($httpRequest, 'fileGetContents'), 50
        );
    }
    
    public function initModules()
    {
        new \Botlife\Application\ModuleLoader();
        
        new MainModule;
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
        $bot->connect('127.0.0.1', 8000);
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

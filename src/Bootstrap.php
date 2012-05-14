<?php

namespace Botlife;

use \Ircbot\Type\Bot as Bot;
use \Ircbot\Application as Ircbot;
use \Botlife\Module\Main as MainModule;
use \Botlife\Module\Misc as MiscModule;
use \Botlife\Module\Math as MathModule;
use \Botlife\Module\Auth as AuthModule;
use \Botlife\Module\Admin as AdminModule;
use \Botlife\Application\Config;

class Bootstrap
{

    public function initConfig()
    {
        Config::addOptions(array(
            'bot.host'     => 'string',
            'bot.port'     => 'number',
            'bnc.password' => 'string',
        ));
        Config::loadFile(realpath(__DIR__ . '/../config.ini'));
    }

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
        $debug = new \Botlife\Debug;
        $color = new \Botlife\Application\Colors;
        $color->output = $color::OUTPUT_ANSI;
        $httpRequest = new Entity\HttpRequest;
        if (function_exists('curl_init')) {
            \DataGetter::addCallback(
            	'file-content', 'curl-content',
                array($httpRequest, 'doCurl'), 25
            );
        } else {
            $debug->log(
        	    'NET', 'HTTP', 'cURL extension not available. '
        	        . 'This ' . $color($color::STYLE_BOLD, 'can')
        	        . ' lead to unexpected results.'
            );
        }
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
        $bot = new Bot();
        $bot->ident = 'BotLife';
        $bot->nickname = 'BotLife';
        $bot->connect(
            Config::getOption('bot.host'), Config::getOption('bot.port')
        );
        if ($bncPassword = Config::getOption('bnc.password')) {
            $bot->sendRawData(
                new \Ircbot\Command\Pass($bncPassword)
            );
        }
        Ircbot::getInstance()->getBotHandler()->addBot($bot);
    }
    
    public function run()
    {
        Ircbot::getInstance()->getLoop()->startLoop();
    }
    
}

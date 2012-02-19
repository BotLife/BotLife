<?php

namespace Botlife\Module;

class AModule extends \Ircbot\Module\AModule
{

    public $events = array(
        'on307'         => 'onIsIdentified',
        'on318'         => 'onWhoisEnd',
    );
    public $commands = array();

    public function __construct()
    {
        \Ircbot\Application::getInstance()->getModuleHandler()
            ->addModuleByObject($this);
        
        foreach ($this->commands as $command) {
            $command = new $command;
            if (is_array($command->regex)) {
                foreach ($command->regex as $regex) {
                    $this->prepareCallback($command, $regex);   
                }
            } else {
                $this->prepareCallback($command, $command->regex);     
            }      
        }
    }
    
    public function prepareCallback($command, $regex)
    {
        $cmdHandler = \Ircbot\Application::getInstance()->getUserCommandHandler()
            ->setDefaultMsgType(TYPE_CHANMSG)
            ->setDefaultScanType(IRCBOT_USERCMD_SCANTYPE_REGEX);
        if ($command->needAuth) {
            $cmdHandler->addCommand(
                array($this, 'callback'), $regex, $cmdHandler->defaultMsgType,
                $cmdHandler->defaultScanType, array($command, $command->action)
            );
        } else {
            $cmdHandler->addCommand(
                array($command, $command->action), $regex,
                $cmdHandler->defaultMsgType, $cmdHandler->defaultScanType
            );  
        }
    }
    
    public function callback($event)
    {
        list($event, $callback) = $event;
        $whoises = \Botlife\Application\Storage::loadData('whois-db');
        $bot = \Ircbot\Application::getInstance()->getBotHandler()
            ->getBotById($event->botId);
        $bot->sendRawData(
            'WHOIS ' . $event->mask->nickname . "\r\n"
        );
        $hash = md5($event->mask->nickname . ';' . $event->botId);
        $whoises->$hash->event = $event;
        $whoises->$hash->callback = $callback;
        \Botlife\Application\Storage::saveData('whois-db', $whoises);
    }
    
    public function onIsIdentified($event)
    {
        $hash = md5(\Ircbot\token('0') . ';' . $event->botId);
        $whoises = \Botlife\Application\Storage::loadData('whois-db');
        $whoises->$hash->identified = true;
        \Botlife\Application\Storage::saveData('whois-db', $whoises);
    }
    
    public function onWhoisEnd($event)
    {
        $hash = md5(\Ircbot\token('0') . ';' . $event->botId);
        $whoises = \Botlife\Application\Storage::loadData('whois-db');
        if (!isset($whoises->$hash)) {
            return false;
        }
        $whois = $whoises->$hash;
        unset($whoises->$hash);
        \Botlife\Application\Storage::saveData('whois-db', $whoises);
        
        if (isset($whois->identified)) {
            $whois->event->auth = \Ircbot\token('0');
        }
        
        call_user_func($whois->callback, $whois->event);
    }

}

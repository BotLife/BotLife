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
        $commands = \Botlife\Application\Storage::loadData('commands');
        if (!isset($commands->data)) {
            $commands->data = array();
        }
        \Botlife\Application\Storage::saveData('commands', $commands);
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
        if ($command->needsOp) {
            $messageType = TYPE_CHANMSG;
        } else {
            $messageType = TYPE_MSG;
        }
        $cmdHandler = \Ircbot\Application::getInstance()->getUserCommandHandler()
            ->setDefaultMsgType($messageType)
            ->setDefaultScanType(IRCBOT_USERCMD_SCANTYPE_REGEX);
        $cmdHandler->addCommand(
            array($this, 'callback'), $regex, $cmdHandler->defaultMsgType,
            $cmdHandler->defaultScanType, $command
        );
    }
    
    public function callback($event)
    {
        list($event, $command) = $event; 
        $commands = \Botlife\Application\Storage::loadData('commands');
        $channels = \Botlife\Application\Storage::loadData('channels');
        if (!$commands->data[get_class($command)]->enabled) {
            \Ircbot\notice($event->mask->nickname, 'This command is disabled');
            return;
        }
        if (substr($event->target, 0, 1) == '#') {
            $channel = \Ircbot\Application::getInstance()->getChannelHandler()
                ->getChan($event->target, $event->botId);
            if (!$channels[strtolower($event->target)]
                ->commands[strtolower($command->code)]->enabled) {
                \Ircbot\notice($event->mask->nickname, 'This command is disabled');
                return;
            }    
            if ($command->needsOp && !$channel->isOp($event->mask->nickname)) {
                \Ircbot\notice(
                    $event->mask->nickname,
                    'You need to be op to use this command.'
                );
                return;
            }
        }
        if ($command->needsAdmin) {
            $command->needsAuth = true;
        }
        if ($command->needsSpamfilter) {
            $spamfilter = new \Botlife\Application\Spamfilter;
            if (!$spamfilter->checkCommand($event)) {
                return;
            }
        }
        if ($command->needsAuth) {
            $whoises = \Botlife\Application\Storage::loadData('whois-db');
            $bot = \Ircbot\Application::getInstance()->getBotHandler()
                ->getBotById($event->botId);
            $bot->sendRawData(
                'WHOIS ' . $event->mask->nickname . "\r\n"
            );
            $hash = md5($event->mask->nickname . ';' . $event->botId);
            $whoises->$hash->event = $event;
            $whoises->$hash->command = $command;
            $whoises->$hash->event->matchesB = $event->matches;
            \Botlife\Application\Storage::saveData('whois-db', $whoises);
        } else {
            $class = new $command;
            $this->callCommand($command, $event);
        }
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
        } else {
            $whois->event->auth = null;
        }
        if ($whois->command->needsAdmin) {
            $admins = array('marlinc', 'adrenaline', 'classicrock');
            if (strtolower($whois->event->target) != '#botlife.team') {
                return;
            }
            if (!in_array(strtolower($whois->event->auth), $admins)) {
                return;
            }
        }
        
        $identifiers = \Ircbot\Application::getInstance()
            ->getIdentifierHandler();
        $identifiers->botId = $whois->event->botId;
        \Ircbot\Utility\String::tokenize($whois->event->message);
        $identifiers->set($whois->event->getIdentifiers());
        
        $whois->event->matches = $whois->event->matchesB;
        
        $this->callCommand($whois->command, $whois->event);
    }
    
    public function callCommand($command, $event)
    {
        $command->{$command->action}($event);
        foreach ($command->responses as $response) {
            if (substr($event->target, 0, 1) == '#') {
                if ($command->responseType == $command::RESPONSE_PUBLIC) {
                    \Ircbot\msg($event->target, $response);
                } elseif ($command->responseType == $command::RESPONSE_PRIVATE) {
                    \Ircbot\notice($event->mask->nickname, $response);
                } 
            } else {
                \Ircbot\msg($event->mask->nickname, $response);
            }
        }
        $command->responses = array();
    }

}

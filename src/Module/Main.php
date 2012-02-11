<?php

namespace Botlife\Module;

use \IRCBot_Application as Ircbot;

class Main extends AModule
{

    public $events = array(
        'onConnect'       => 'onConnect',
        'on251'           => 'setNetwork',
    );
    
    public function __construct()
    {
        Ircbot::getInstance()->getModuleHandler()->addModuleByObject($this);
    }

    public function onConnect()
    {
        joinChan('#Dutch');
    }
    
    public function setNetwork()
    {
        $bot = Ircbot::getInstance()->getBotHandler()->getBotById(botId());
        $network = new \Botlife\Network\ANetwork;
        $network->convertIrcbotNetwork($bot->currentNetwork);
        $bot->currentNetwork = $network;
    }

}

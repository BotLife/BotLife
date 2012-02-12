<?php

namespace Botlife\Module;

use \Ircbot\Application as Ircbot;

class Main extends AModule
{

    public $events = array(
        'on251'           => 'setNetwork',
        'onConnect',
    );
    
    public function __construct()
    {
        Ircbot::getInstance()->getModuleHandler()->addModuleByObject($this);
    }

    public function onConnect()
    {
        \Ircbot\joinChan('#Dutch');
    }
    
    public function setNetwork()
    {
        $bot = Ircbot::getInstance()->getBotHandler()
            ->getBotById(\Ircbot\botId());
        $network = new \Botlife\Network\ANetwork;
        $network->convertIrcbotNetwork($bot->currentNetwork);
        $bot->currentNetwork = $network;
    }

}

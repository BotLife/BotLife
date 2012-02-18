<?php

namespace Botlife\Module;

use \Ircbot\Application as Ircbot;

class Main extends AModule
{

    public $events = array(
        'on251'           => 'setNetwork',
        'loopIterate',
        'onConnect',
    );

    public $_oldTime;

    public function onConnect()
    {
        \Ircbot\joinChan('#BotLife,#BotLife.Team,#snelle');
    }
    
    public function setNetwork()
    {
        $bot = Ircbot::getInstance()->getBotHandler()
            ->getBotById(\Ircbot\botId());
        $network = new \Botlife\Network\ANetwork;
        $network->convertIrcbotNetwork($bot->currentNetwork);
        $bot->currentNetwork = $network;
    }
    
    public function loopIterate()
    {
        if (!$this->_oldTime) {
            $this->_oldTime = time();
        }
        if ((time() - $this->_oldTime) >= 3) {
            \Botlife\Application\Spamfilter::decreaseAmount();
            $this->_oldTime = time();
        }
    }

}

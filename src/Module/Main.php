<?php

namespace Botlife\Module;

use \Ircbot\Application as Ircbot;

class Main extends AModule
{

    public $events = array(
        'on251'           => 'setNetwork',
        'loopIterate',
        'onConnect',
        'onCtcpRequest'
    );

    public $_oldTime = array();

    public function onConnect()
    {
        \Ircbot\joinChan('#BotLife,#BotLife.Team');
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
        if (empty($this->_oldTime)) {
            $this->_oldTime[0] = time();
            $this->_oldTime[1] = time();
        }
        if ((time() - $this->_oldTime[0]) >= 3) {
            $spamfilter = new \Botlife\Application\Spamfilter;
            $spamfilter->decreaseAmount();
            $this->_oldTime[0] = time();
        }
        if ((time() - $this->_oldTime[1]) >= 60) {
            \Botlife\Application\Storage::saveToDisk();
            $this->_oldTime[1] = time();
        }
    }
    
    public function onCtcpRequest(\Ircbot\Command\CtcpRequest $event)
    {
        if ($event->message == 'VERSION') {
            $reply = new \Ircbot\Command\CtcpReply(
            $event->mask->nickname,
                'VERSION BotLife version ' . `git describe`
            );
            $bot = Ircbot::getInstance()->getBotHandler()
            ->getBotById($event->botId);
            $bot->sendRawData($reply);
        }
    }

}

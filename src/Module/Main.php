<?php

namespace Botlife\Module;

use \Ircbot\Application as Ircbot;

class Main extends AModule
{

    public $events = array(
        'on251'           => 'setNetwork',
        'loopIterate',
        'onConnect',
        'onDisconnected',
    );

    public $_oldTime = array();

    public function onConnect()
    {
        \Ircbot\joinChan('#BotLife,#BotLife.Team');
    }
    
    public function onDisconnected($botId)
    {
        $bot = Ircbot::getInstance()->getBotHandler()
            ->getBotById($botId);
        if (!$bot) {
            return;
        }
        // Little hack to close the socket
        \Ircbot\Application::getInstance()->getSocketHandler()
            ->getSocketById(1)->close();
        $bot->connect($bot->serverAddress, $bot->serverPort);
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

}

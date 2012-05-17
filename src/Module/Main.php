<?php

namespace Botlife\Module;

use \Ircbot\Application as Ircbot;

class Main extends AModule
{

    public $events = array(
        'on251'           => 'setNetwork',
        'onConnect',
        'onCtcpRequest'
    );
    
    public function __construct()
    {
        $spamfilter = new \Botlife\Application\Spamfilter;
        $timer1 = new \Ircbot\Entity\Timer(
            'spamfilter-decrease', array($spamfilter, 'decreaseAmount'), 3000
        );
        $timer2 = new \Ircbot\Entity\Timer(
            'storage-save', '\Botlife\Application\Storage::saveToDisk', 50000
        );
        \Ircbot\Handler\Timer::addTimer($timer1);
        \Ircbot\Handler\Timer::addTimer($timer2);
    }

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

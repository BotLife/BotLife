<?php

namespace Botlife\Module;

class Admin extends AModule
{

    public $events = array(
        'spamfilterCaughtHost',
        'spamfilterCaughtChannel'
    );
    public $commands = array(
        '\Botlife\Command\Spamfilter',
        '\Botlife\Command\Admin\Command',
    );
    
    public function spamfilterCaughtHost($data)
    {
        list($host, $command) = $data;
        \Ircbot\Msg(
            '#BotLife.Team',
            'Caught the hostname of user ' . $command->mask . ' for flooding '
                . 'command ' . $command->message . '.'
        );
    }
    
    public function spamfilterCaughtChannel($data)
    {
        list($channel, $command) = $data;
        \Ircbot\Msg(
            '#BotLife.Team',
            'Caught user ' . $command->mask . ' in ' . $channel . ' for '
                . 'flooding command ' . $command->message . '.'
        );
    }

}

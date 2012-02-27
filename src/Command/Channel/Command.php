<?php

namespace Botlife\Command\Channel;

class Command extends \Botlife\Command\ACommand
{

    public $regex   = array(
        '/^BotLife(,|:)? (?P<action>disable|enable) command( )?(?P<payload>.*)?/i'
    );
    public $needsOp = true;

    public function run($event)
    {
        $channels = \Botlife\Application\Storage::loadData('channels');
        $command  = $event->matches['payload'];
        if (!$this->commandExists($command)) {
            $this->respond('No such command. Ask in #BotLife for help.');
            return;
        }
        if (strtoupper($event->matches['action']) == 'DISABLE') {
            $channels[strtolower($event->target)]
                ->commands[$event->matches['payload']]->enabled = false;
            $this->respond('Disabled command \'' . $command . '\'.');
        } elseif (strtoupper($event->matches['action']) == 'ENABLE') {
            $channels[strtolower($event->target)]
                ->commands[$event->matches['payload']]->enabled = true;
            $this->respond('Enabled command \'' . $command . '\'.');
        }
        \Botlife\Application\Storage::saveData('channels', $channels);
    }
    
    public function commandExists($command)
    {
        $commands = \Botlife\Application\Storage::loadData('commands');
        foreach ($commands->data as $data => $options) {
            $tmp = new $data;
            if (strtolower($tmp->code) == strtolower($command)) {
                return true;
            }
        }
        return false;
    }

}

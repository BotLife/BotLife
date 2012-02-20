<?php

namespace Botlife\Command\Admin;

class Command extends \Botlife\Command\ACommand
{

    public $regex     = array(
        '/^COMMAND (?P<option>DISABLE|ENABLE) (?P<command>.+)$/i',
    );
    public $action    = 'run';
    public $needsAuth = true;
    
    public function run($event)
    {
        if (strtolower($event->target) != '#botlife.team') {
            return;
        }
        if (!in_array(strtolower($event->auth), array('marlinc', 'adrenaline'))) {
            return;
        }
        $option = strtoupper($event->matches['option']);
        if ($option == 'DISABLE') {
            $commands = \Botlife\Application\Storage::loadData('commands');
            $command = 'Botlife\Command\\' . $event->matches['command'];
            if (!in_array($command, get_declared_classes())) {
                \Ircbot\msg(
                    '#BotLife.Team',
                    'Cannot find the command ' . $command . '.' 
                );
                return;
            }
            $commands->data[$command]->enabled = false;
            \Ircbot\msg(
                    '#BotLife.Team',
                    'Disabled command ' . $command . '.' 
                );
            \Botlife\Application\Storage::saveData('commands', $commands);
            
        } elseif ($option == 'ENABLE') {
            $commands = \Botlife\Application\Storage::loadData('commands');
            $command = 'Botlife\Command\\' . $event->matches['command'];
            if (!in_array($command, get_declared_classes())) {
                \Ircbot\msg(
                    '#BotLife.Team',
                    'Cannot find the command ' . $command . '.' 
                );
                return;
            }
            $commands->data[$command]->enabled = true;
            \Ircbot\msg(
                    '#BotLife.Team',
                    'Enabled command ' . $command . '.' 
            );
            \Botlife\Application\Storage::saveData('commands', $commands);
        }
    }

}

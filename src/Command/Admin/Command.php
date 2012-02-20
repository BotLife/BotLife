<?php

namespace Botlife\Command\Admin;

class Command extends \Botlife\Command\ACommand
{

    public $regex      = array(
        '/^COMMAND( )?(?P<option>DISABLE|ENABLE|STATUS)?/i',
    );
    public $action     = 'run';
    public $needsAdmin = true;
    
    public function run($event)
    {
        if (isset($event->matches['option'])) {
            $option = strtoupper($event->matches['option']);
        } else {
            $option = 'STATUS';
        }
        if ($option == 'DISABLE') {
            $commands = \Botlife\Application\Storage::loadData('commands');
            $command = 'Botlife\Command\\' . \Ircbot\token('2');
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
            $command = 'Botlife\Command\\' . \Ircbot\token('2');
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
        } elseif ($option == 'STATUS') {
            $commands = \Botlife\Application\Storage::loadData('commands');
            $amountEnabled = 0;
            foreach ($commands->data as $command => $options) {
                if ($options->enabled) {
                    ++$amountEnabled;
                }
            }
            \Ircbot\msg(
                '#Botlife.Team',
                $amountEnabled . '\\' . count($commands->data) . ' enabled'
            );
        }
    }

}

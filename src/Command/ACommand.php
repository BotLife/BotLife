<?php

namespace Botlife\Command;

abstract class ACommand
{

    public $needsSpamfilter = true;
    public $needsAuth       = false;
    public $needsAdmin      = false;
    
    public function __construct()
    {
        $commands = \Botlife\Application\Storage::loadData('commands');
        if (!isset($commands->data[get_class($this)])) {
        
            $command = new \StorageObject;
            $command->enabled = true;
            
            $commands->data[get_class($this)] = $command;
        }
        \Botlife\Application\Storage::saveData('commands', $commands);
    }

}

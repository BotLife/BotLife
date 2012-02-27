<?php

namespace Botlife\Command;

abstract class ACommand
{

    const RESPONSE_PUBLIC   = 1;
    const RESPONSE_PRIVATE  = 2;

    public $code            = null;
    public $action          = 'run';

    public $needsSpamfilter = true;
    public $needsAuth       = false;
    public $needsAdmin      = false;
    public $needsOp         = false;
    
    public $responseSymbols = array(
        self::RESPONSE_PUBLIC  => array('@'),
        self::RESPONSE_PRIVATE => array('.', '!'),
    );
    public $responseType    = self::RESPONSE_PRIVATE;
    public $responses       = array();
    
    public function __construct()
    {
        $commands = \Botlife\Application\Storage::loadData('commands');
        if (!isset($commands[get_class($this)])) {
        
            $command = new \StorageObject;
            $command->enabled = true;
            
            $commands[get_class($this)] = $command;
        }
        \Botlife\Application\Storage::saveData('commands', $commands);
    }
    
    public function respond($message)
    {
        $this->responses[] = $message;
    }
    
    public function detectResponseType($command)
    {
        $symbol = substr($command, 0, 1);
        foreach ($this->responseSymbols as $type => $symbols) {
            if (in_array($symbol, $symbols)) {
                $this->responseType = $type;
            }
        }
    }

}

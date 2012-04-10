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
    
    public function respondWithInformation($information, $code = null)
    {
        $c = new \Botlife\Application\Colors;
        $response = '';
        $first    = true;
        foreach ($information as $key => $value) {
            if (!$first) {
                if (strlen($response) >= 200) {
                    $this->respondWithPrefix($response, $code);
                    $response = '';
                } else {
                    $response .= $c(12, ' - ');
                }
            }
            $first = false;
            $response .= $c(12, $key) . ': ' . ((!is_array($value)) ? $c(3, $value) : '');
            if (!is_array($value)) continue;
            
            $response .= $c(3, $value[0]) . $c(12, '(');
            $arraylen = count($value[1]);
            $i        = 0;
            foreach ($value[1] as $key2 => $value2) {
                if (is_string($key2)) $response .= $c(12, $key2 . ': ') . $c(3, $value2);
                else                  $response .= $c(3,  $value2);
                if (++$i < $arraylen) $response .= $c(12, '/');
            }
            $response .= $c(12, ')');
        }
        if (strlen($response) > 0)
            $this->respondWithPrefix($response, $code);
    }
        
    public function respondWithPrefix($message, $prefix = null)
    {
        $c = new \Botlife\Application\Colors;
        if (!$prefix) {
            $prefix = strtoupper($this->code);
        }
        $messages = explode(PHP_EOL, $message);
        foreach ($messages as $message) {
            $response = $c(12, '[') . $c(3, $prefix) . $c(12, '] ');
            $response .= $message;
            $this->respond($response);
        }
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

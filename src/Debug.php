<?php

namespace Botlife;

class Debug extends \Ircbot\Application\Debug\ADebug
{

    public function log($category, $type, $message,
        $level = IRCBOT_DEBUG_NORMAL) {
        if (func_num_args() == 3 || func_num_args() == 4) {
            list($category, $type, $message) = func_get_args();
            if (func_num_args() == 4) {
                $level = func_get_arg(3);
            }
        } elseif (func_num_args() == 2) {
            $category = 'System';
            $type     = 'Daemon';
            $message  = func_get_arg(0);
        }
        $c = new Application\Colors;
        $c->output = Application\Colors::OUTPUT_ANSI;
        file_put_contents(
            'botlife/log',
            '[' . $category . '|' . $type . '] ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }
    
    public function getSign($c, $category, $type)
    {
        return $c($c::STYLE_BOLD) . $c(12) . '[' . $c(3, $category) . $c(12) . '|'
            . $c(3, $type) . $c(12) . ']' . $c(12);
    }

}

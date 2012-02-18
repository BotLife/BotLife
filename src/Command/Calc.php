<?php

namespace Botlife\Command;

class Calc
{

    const ERR_DEVIDEBYZERO = 1;
    const ERR_SQRTNEGATIVE = 2;
    const ERR_INTERNALERROR = 4;
    const ERR_UNDEFINEDVARIABLE = 8;
    
    public $regex = '/^([.!@]calc |`)(?P<exp>.+)$/';
    public $action = 'calc';
    
    public $lastCalcErrors = 0;
    
    public function calc($event)
    {
        $spamfilter = new \Botlife\Application\Spamfilter;
        if (!$spamfilter->checkCommand($event)) {
            return;
        }
        $time = array($this->measureTime());
        $math = new \Botlife\Utility\Math;
        $exp = $event->matches['exp'];
        set_error_handler(array($this, 'handleErrors'));
        $data = $math->evaluate($exp);
        $response = '[Calc] ';
        if (is_numeric($data)) {
            $response .= $exp . ' = ' . number_format($data, 2, ',', '.') . ' (' . $math->alphaRound($data) . ')';
        } else {
            $response .= 'Could not execute your expression because ';
            if ($this->lastCalcErrors & self::ERR_DEVIDEBYZERO) {
                $response .= 'you tried to divide by zero.';
            } elseif ($this->lastCalcErrors & self::ERR_SQRTNEGATIVE) {
                $response .= 'you tried to do a square root with a negative number.';
            } elseif ($this->lastCalcErrors & self::ERR_INTERNALERROR) {
                $response .= 'of a internet error.';
            } elseif ($this->lastCalcErrors & self::ERR_UNDEFINEDVARIABLE) {
                $response .= 'you defined a unknown variable.';
            } else {
                $response .= 'of a unknown error.';
            }
        }
        $time[] = $this->measureTime();
        \Ircbot\msg($event->target, $response . '(' . round(($time[1] - $time[0]) * 1000, 2) . 'ms)');
        restore_error_handler();
        $this->lastCalcErrors = 0;
    }
    
    public function measureTime()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        return $time;
    }
    
    public function handleErrors()
    {
        $errStr = func_get_arg(1);
        if (stristr($errStr, 'division by zero')) {
            $this->lastCalcErrors |= self::ERR_DEVIDEBYZERO;
        } elseif (stristr($errStr, 'square root of negative number')) {
            $this->lastCalcErrors |= self::ERR_SQRTNEGATIVE;
        } elseif (stristr($errStr, 'internal error')) {
            $this->lastCalcErrors |= self::ERR_INTERNALERROR;
        } elseif (stristr($errStr, 'undefined variable')) {
            $this->lastCalcErrors |= self::ERR_UNDEFINEDVARIABLE;
        } else {
            var_dump(func_get_args());
        }
    }

}

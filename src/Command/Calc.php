<?php

namespace Botlife\Command;

class Calc extends ACommand
{

    const ERR_DEVIDEBYZERO = 1;
    const ERR_SQRTNEGATIVE = 2;
    const ERR_INTERNALERROR = 4;
    const ERR_UNDEFINEDVARIABLE = 8;
    
    public $regex = '/^([.!@](?P<type>calc|eval)( )?|`)(?P<exp>.+)?$/';
    public $action = 'calc';
    
    public $lastCalcErrors = 0;
    
    public function calc($event)
    {
        $c = new \Botlife\Application\Colors;
        if (!isset($event->matches['exp'])) {
            \Ircbot\Notice(
                $event->mask->nickname,
                $c(12, '[') . $c(3, 'CALC') . $c(12, '] ')
                    . $c(12, 'You need to specify a expression. For example: ')
                    . $c(3, '!calc 5^3')
            );  
            return;
        }
        $calc = \Botlife\Application\Storage::loadData('math-calc');
        if (!isset($calc->cache)) {
            $calc->cache = array();
        }
        $time = array($this->measureTime());
        
        if (isset($event->matches['type'])) {
            $type = $event->matches['type'];
        } else {
            $type = 'calc';
        }
        $type = strtoupper($type);
        $hash = md5($event->mask->nickname);
        if (isset($calc->exp->$hash)) {
            $math = $calc->exp->$hash;
        } else {
            $math = new \Botlife\Utility\Math;
        }
        set_error_handler(array($this, 'handleErrors'));
        $exp = $event->matches['exp'];
        $response = $c(12, '[') . $c(3, 'CALC') . $c(12, '] ');
        if ($type == 'EVAL') {
            $response .= $c(12, 'Executed expression: ') . $c(3, $exp)
                . $c(12, '. ') . $c(12, 'You can now use your expression in ')
                . $c(3, '!calc') . $c(12, '.');
            $math->evaluate($exp);
        } else {
            /*if (!isset($calc->cache[$exp])) {
                $data = $math->evaluate($exp);
                $calc->cache[$exp] = $data;
            } else {
                $data = $calc->cache[$exp];
            }*/
            $data = $math->evaluate($exp);
            $math->setConstant('ans', $data);
            if (is_numeric($data)) {
                $response .= $c(3, $exp) . $c(12, ' = ')
                    . $c(3, number_format($data, 2, ',', '.')) . $c(12, ' (')
                    . $c(3, $math->alphaRound($data)) . $c(12, ')');
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
        }
        $calc->exp->$hash = $math;
        $time[] = $this->measureTime();
        \Ircbot\msg(
            $event->target,
            $response . $c(12, ' (')
                . $c(3, round(($time[1] - $time[0]) * 1000, 2)) . $c(12, 'ms)')
        );
        \Botlife\Application\Storage::saveData('math-calc', $calc);
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

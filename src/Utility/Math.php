<?php

namespace Botlife\Utility;

class Math
{

    public function evaluate($formula)
    {
        var_dump($formula);
        $math = new \EvalMath; 
        $math->fb = array('abs', 'sqrt');
        return $math->evaluate($formula);
    }
    
    public function alphaRound($val, $precision = 2)
    {
        var_dump($val);
        if ($val < 1000) {
            return $val;
        }
        elseif ($val < 1000*1000 && $val >= 1000) {
            return number_format($val / (1000), 2) . 'k';
        } 
        elseif ($val < 1000*1000*1000 && $val >= 1000*1000) {
            return number_format($val / (1000*1000), 2) . 'm';
        } 
    }
    
    static public function sqrt($int)
    {
        return bcsqrt($int);
    }
    
    static public function abs($int)
    {
        return abs($int);
    }

}

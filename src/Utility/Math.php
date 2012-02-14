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
        $units = array('k', 'm', 'b', 't');
        if ($val < 1000) {
            return $val;
        }
        foreach ($units as $key => $unit) {
            $start = pow(1000, $key + 1);
            if ($val > $start && $key + 1 != count($units)) {
                continue;
            }
            return number_format($val / $start, $precision, ',', '.') . $unit;
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

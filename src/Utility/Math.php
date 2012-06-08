<?php

namespace Botlife\Utility;

class Math
{

    public $units = array('k', 'm', 'b', 't');

    private $_math;

    public function evaluate($formula)
    {
        if (!$this->_math) {
            $this->_math = new \EvalMath; 
        }
        foreach ($this->units as $key => $unit) {
            $this->_math->v[$unit] = pow(1000, $key + 1);
        }
        return $this->_math->evaluate($formula);
    }
    
    public function setConstant($key, $value)
    {
        $this->_math->v[$key] = $value;
        if (!in_array($key, $this->_math->vb)) {
            $this->_math->vb[] = $key;
        }
    }
    
    public function alphaRound($val, $precision = 2)
    {
        $prefix = ($val < 0) ? '-' : '';
        $val = abs($val);
        if ($val < 1000) {
            return $val;
        }
        $suffix = '';
        foreach ($this->units as $key => $unit) {
            $val = $val / 1000;
            if ($val >= 1000) continue;
            break;
        }
        $suffix = '';
        if ($val >= 1000) {
            $suffix = ' * ' . $this->alphaRound($val, 0);
            $val = $val / pow(10, log10($val)); //Change number to a number under the 10.
        }
        
        return $prefix . number_format($val, $precision, ',', '.')
            . $unit . $suffix;
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


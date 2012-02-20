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
        if ($val < 1000) {
            return $val;
        }
        foreach ($this->units as $key => $unit) {
            $start = pow(1000, $key + 1);
            if ($val > $start && $key + 1 != count($this->units)) {
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

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
            $start = pow(1000, $key + 1);
            if ($key + 1 == count($this->units) && $val >= ($start * 1000)) {
                $tmp = 1;
                while (true) {
                    if (($val / $tmp) < 1000) {
                        $div = $tmp / pow(1000, $key + 1);
                        break;
                    }
                    $tmp *= 1000; 
                }
                $suffix = ' * ' .$this->alphaRound($div, 0);
                $val /= $div;
            } 
            if ($val >= ($start * 1000) && $key + 1 != count($this->units)) {
                continue;
            }
            $text = $prefix . number_format($val / $start, $precision, ',', '.')
                . $unit . $suffix;
            return $text;
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

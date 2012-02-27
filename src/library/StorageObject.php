<?php

class StorageObject extends ArrayObject
{
    
    private $_data;
    
    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }
    
    public function __get($key)
    {
        if (!isset($this->_data[$key])) {
            $this->_data[$key] = new self;
        }
        return $this->_data[$key];
    }
    
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }
    
    public function offsetExists($key)
    {
        return $this->__isset($key);
    } 
    
    public function offsetGet($key)
    {
        return $this->__get($key);
    }  
    
    public function offsetSet($key, $value)
    {
        $this->__set($key, $value);
    }   
    
}

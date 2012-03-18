<?php

class StorageObject implements ArrayAccess, Serializable
{
    
    protected $_array;
    
    public function __isset($key)
    {
        return isset($this->_array[$key]);
    }
    
    public function __get($key)
    {
        if (!isset($this->_array[$key])) {
            $this->_array[$key] = new self;
        }
        return $this->_array[$key];
    }
    
    public function __set($key, $value)
    {
        $this->_array[$key] = $value;
    }
    
    public function __unset($key)
    {
        unset($this->_array[$key]);
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
       
    public function offsetUnset($key)
    {
        $this->__unset($key);
    }
    
    public function serialize()
    {
        return serialize($this->_array);
    }
    
    public function unserialize($data)
    {
        $this->_array = unserialize($data);
    }
    
}

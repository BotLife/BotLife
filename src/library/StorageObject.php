<?php

class StorageObject
{
    
    public function __isset($key)
    {
        return isset($this->$key);
    }
    
    public function __get($key)
    {
        if (!isset($this->$key)) {
            $this->$key = new self;
        }
        return $this->$key;
    }
    
    public function __set($key, $value)
    {
        $this->$key = $value;
    }   
    
}

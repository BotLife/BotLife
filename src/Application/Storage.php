<?php

namespace Botlife\Application;

class Storage
{
    
    static private $_data;
    
    static public function loadData($key)
    {
        if (!self::$_data) {
            self::loadFromDisk();
        }
        if (!isset(self::$_data[$key])) {
            self::$_data[$key] = new \StorageObject;
        }
        return self::$_data[$key];
    }
    
    static public function saveData($key, $data)
    {
        self::$_data[$key] = $data;
    }   
    
    static public function loadFromDisk()
    {
        if (!file_exists('storage.dat')) {
            return false;
        }
        self::$_data = unserialize(file_get_contents('storage.dat'));
    }
    
    static public function saveToDisk()
    {
        file_put_contents('storage.dat', serialize(self::$_data));
    }
    
}

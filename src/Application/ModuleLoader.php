<?php

namespace Botlife\Application;

class ModuleLoader
{
    
    public function __construct()
    {
        $files = $this->findInfoFiles();
        foreach ($files as $file) {
            $data = parse_ini_file($file);
            if (!$data['autoload']) {
                continue;
            }
            $class = '\Botlife\Module\\' . $data['class'];
            new $class;
        }
    }
    
    public function findDirectories($directory)
    {
        $dir  = @opendir($directory);
        if (!$dir) {
            return array();
        }
        $dirs = array();
        while ($file = readdir($dir)) {
            if (in_array($file, array('.',  '..'))) {
                continue;
            }
            $file = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file)) {
                $dirs[] = $file;
                $dirs   = array_merge($dirs, $this->findDirectories($file));
            }
        }
        return array_unique($dirs);
    }
    
    public function findInfoFiles()
    {
        $directories = explode(PATH_SEPARATOR, get_include_path());
        $dirs        = array();
        foreach ($directories as $directory) {
            $dirs += $this->findDirectories($directory);
        }
        $dirs = array_unique($dirs);
        $files = array();
        foreach ($dirs as $directory) {
            $search = opendir($directory);
            while ($file = readdir($search)) {
                if (!fnmatch('*.info', $file)) {
                    continue;
                }
                $files[] = $directory . DIRECTORY_SEPARATOR . $file;
            }
        }
        return $files;
    }
}
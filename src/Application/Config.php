<?php

namespace Botlife\Application;

class Config
{

    static private $_options;
    static private $_knownOptions = array();
    static private $_types = array(
        'int'       => 'is_int',
        'integer'   => 'is_int',
        'string'    => 'is_string',
        'callable'  => 'is_callable',
        'bool'      => 'is_bool',
        'array'     => 'is_array',
        'object'    => 'is_object',
        'number'    => 'is_numeric',
    );
    
    static public $configExtension = array(
        'json' => self::CONFIG_JSON,
        'ini'  => self::CONFIG_INI,
    );
    
    const CONFIG_INI  = 1;
    const CONFIG_JSON = 2;

    static public function addOption($option, $type = null)
    {
        $data = new \StdClass;
        if (isset(self::$_types[$type])) {
            $data->type = $type;
        } elseif (!$type) {
        } elseif (!isset(self::$_types[$type])) {
            throw new \Exception('Unknown type \'' . $type . '\'!');
        }
        self::$_knownOptions[$option] = $data;
    }
    
    static public function addOptions($options)
    {
        foreach ($options as $key => $value) {
            if (is_int($key)) {
                @list($option, $type) = (array) $value;
            } else {
                $option = $key;
                @list($type) = (array) $value;
            }
            self::addOption($option, $type);
        }
    }

    static public function setOption($option, $value)
    {
        if (!isset(self::$_knownOptions[$option])) {
            throw new \Exception('Unknown setting \'' . $option . '\'.');
        }
        if (!self::_checkType($option, $value)) {
            throw new \Exception('Wrong type');
        }
        $path = explode('.', $option);
        if (!self::$_options) {
            self::$_options = new \StdClass;
        }
        $section = &self::$_options;
        foreach ($path as $part) {
            $section = &$section->{$part};
        }
        $section = $value;
    }
    
    static public function setOptions($options)
    {
        foreach ($options as $option => $value) {
            self::setOption($option, $value);
        }
    }
    
    static public function getOption($option)
    {
        if (!isset(self::$_knownOptions[$option])) {
            throw new \Exception('Unknown setting \'' . $option . '\'.');
        }
        $path = explode('.', $option);
        if (!self::$_options) {
            self::$_options = new \StdClass;
        }
        $section = self::$_options;
        foreach ($path as $part) {
            if (!isset($section->{$part})) {
                return false;
            }
            $section = $section->{$part};
        }
        return $section;
    }
    
    static public function loadData($data, $type = self::CONFIG_INI)
    {
        $options = array();
        switch ($type) {
            case self::CONFIG_JSON:
                $parsed = json_decode($data);
                throw new \Exception('Not implemented yet.');
                break;
            case self::CONFIG_INI:
            default:
                $parsed = parse_ini_string($data, true);
                foreach ($parsed as $key => $value) {
                    if (is_array($value)) {
                        $numberKeys = true;
                        foreach ($value as $subKey => $subValue) {
                            if (!is_int($subKey)) {
                                $numberKeys = false;
                            }
                        }
                        if ($numberKeys) {
                            $options[$key] = $value;
                        } else {
                            foreach ($value as $subKey => $subValue) {
                                $option = $key . '.' . $subKey;
                                $options[$option] = $subValue;
                            }
                        }
                    } else {
                        $options[$key] = $value;
                    }
                }
                break;
        }
        self::setOptions($options);
    }
    
    static public function loadFile($file)
    {
        $extension = array_pop(explode('.', $file));
        if (!file_exists($file)) {
            throw new \Exception('The specified file doesn\'t exist!');
        }
        if (!isset(self::$configExtension[$extension])) {
            throw new \Exception('Unknown config format');
        }
        $raw = file_get_contents($file);
        $type = self::$configExtension[$extension];
        return self::loadData($raw, $type);
    }
    
    static public function export($return = false)
    {
        return var_export(self::$_options, $return);
    }
    
    static public function dump($return = false)
    {
        return print_r(self::$_options, $return);
    }
    
    static protected function _checkType($option, $value)
    {
        $option = self::$_knownOptions[$option];
        if (isset($option->type)) {
            $check = self::$_types[$option->type];
            return call_user_func($check, $value);
        }
        return true;
    }

}

if ($_SERVER['argv'][0] == basename(__FILE__)) {
    Config::addOptions(
        array(
            'section.subsection.string'   => 'string',
            'section.subsection.integer'  => 'int',
            'section.subsection.function' => 'callable',
            'section.subsection.array'    => 'array',
            'section.subsection.setting',
        )
    );
    $config  = 'section.subsection.string = a string' . PHP_EOL;
    $config .= '[section]'                            . PHP_EOL;
    $config .= 'subsection.array[] = 1'               . PHP_EOL;
    $config .= 'subsection.array[] = 2'                . PHP_EOL;
    Config::loadData($config, Config::CONFIG_INI);

    Config::setOption('section.subsection.integer',  100);
    Config::setOption('section.subsection.function', 'file_get_contents');
    Config::setOption('section.subsection.setting',  'Whatever');

    Config::dump();
}

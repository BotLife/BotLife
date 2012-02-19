<?php

namespace Botlife\Application;

class Spamfilter
{

    private $_enabled;

    private $_hostFilter    = array();
    private $_channelFilter = array();
    private $_ignore        = array();
    
    public function __construct()
    {
        $data = \Botlife\Application\Storage::loadData('spamfilter');
        $this->_enabled       = (bool) $data->enabled;
        $this->_hostFilter    = (array) $data->filter->host;
        $this->_channelFilter = (array) $data->filter->channel;
        $this->_ignore        = (array) $data->ignore;
    }

    public function checkCommand(\Ircbot\Type\MessageCommand $command)
    {
        if (!$this->_enabled) {
            return true;
        }
        $host = strtolower($command->mask->host);
        foreach ($this->_ignore as $pattern) {
            if (fnmatch($pattern, $command->mask)) {
                return;
            }
        }
        if (isset($this->_hostFilter[$host])) {
            ++$this->_hostFilter[$host];
        } else {
            $this->_hostFilter[$host] = 1;
        }
        if (substr($command->target, 0, 1) == '#') {
            $channel = strtolower($command->target);
            if (isset($this->_channelFilter[$channel])) {
                ++$this->_channelFilter[$channel];
            } else {
                $this->_channelFilter[$channel] = 1;
            }
            if ($this->_channelFilter[$channel] >= 3) {
                return false;
            }
        }
        if ($this->_hostFilter[$host] >= 3) {
            return false;
        }
        return true;
    }
    
    public function decreaseAmount()
    {
        foreach ($this->_hostFilter as $host => &$commands) {
            --$commands;
            if ($commands == 0) {
                unset($this->_hostFilter[$host]);
            }
        }
        foreach ($this->_channelFilter as $channel => &$commands) {
            --$commands;
            if ($commands == 0) {
                unset($this->_channelFilter[$channel]);
            }
        }
    }
    
    public function __destruct()
    {
        $data = \Botlife\Application\Storage::loadData('spamfilter');
        $data->enabled         = $this->_enabled;
        $data->filter->host    = $this->_hostFilter;
        $data->filter->channel = $this->_channelFilter;
        $data->ignore          = $this->_ignore;
        \Botlife\Application\Storage::saveData('spamfilter', $data);
    }

}

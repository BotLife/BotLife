<?php

namespace Botlife\Module;

use \IRCBot_Application as Ircbot;

class Main extends AModule
{

    public $events = array(
        'onConnect'       => 'onConnect',
    );
    
    public function __construct()
    {
        Ircbot::getInstance()->getModuleHandler()->addModuleByObject($this);
    }

    public function onConnect()
    {
        joinChan('#Dutch');
    }

}

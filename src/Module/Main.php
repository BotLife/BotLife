<?php

namespace Botlife\Module;

class Main extends AModule
{

    public $events = array(
        'onConnect'       => 'onConnect',
    );
    
    public function __construct()
    {
        \IRCBot_Application::getInstance()->getModuleHandler()
            ->addModuleByObject($this);
    }

    public function onConnect()
    {
        joinChan('#Dutch');
    }

}

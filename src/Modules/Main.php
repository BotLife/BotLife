<?php

class BotLife_Modules_Main extends BotLife_Modules_Abstract
{

    public $events = array(
        'onConnect'       => 'onConnect',
    );
    
    public function __construct()
    {
        IRCBot_Application::getInstance()->getModuleHandler()
            ->addModuleByObject($this);
    }

    public function onConnect()
    {
        joinChan('#Dutch');
    }

}

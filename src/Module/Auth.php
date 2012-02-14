<?php

namespace Botlife\Module;

class Auth
{

    public function __construct()
    {
        \Ircbot\Application::getInstance()->getUserCommandHandler()
            ->setDefaultMsgType(TYPE_PRIVNOTICE)
            ->setDefaultScanType(IRCBOT_USERCMD_SCANTYPE_WILDCRD)
            ->addCommand(array($this, 'auth'), '*please choose a different *');
    }
    
    public function auth($event)
    {
        if ($event->mask->nickname != 'NickServ') {
            return;
        }
        
        include 'config.php';
        \Ircbot\msg($event->mask->nickname, 'ID ' . $ns_pass);
        
    }

}

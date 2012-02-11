<?php

namespace Botlife\Network;

class ANetwork extends \IRCBot_Types_Network
{
    public $name;
    
    public function convertIrcbotNetwork(\IRCBot_Types_Network $network)
    {
        $this->name = $network->name;
        $this->hostname = $network->hostname;
        $this->iSupport = $network->iSupport;
    }
}

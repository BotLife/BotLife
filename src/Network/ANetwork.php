<?php

namespace Botlife\Network;

class ANetwork extends \Ircbot\Type\Network
{
    public $name;
    
    public function convertIrcbotNetwork(\Ircbot\Type\Network $network)
    {
        $this->name = $network->name;
        $this->hostname = $network->hostname;
        $this->iSupport = $network->iSupport;
    }
}

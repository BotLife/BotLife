<?php

namespace Botlife\Module;

class AModule extends \Ircbot\Module\AModule
{

    public function __construct()
    {
        \Ircbot\Application::getInstance()->getModuleHandler()
            ->addModuleByObject($this);
    }

}

<?php

namespace Botlife\Module;

class AModule extends \Ircbot\Module\AModule
{
    
    public $commands = array();

    public function __construct()
    {
        \Ircbot\Application::getInstance()->getModuleHandler()
            ->addModuleByObject($this);
        $cmdHandler = \Ircbot\Application::getInstance()->getUserCommandHandler()
            ->setDefaultMsgType(TYPE_CHANMSG)
            ->setDefaultScanType(IRCBOT_USERCMD_SCANTYPE_REGEX);
        foreach ($this->commands as $command) {
            $command = new $command;
            if (is_array($command->regex)) {
                foreach ($command->regex as $regex) {
                    $cmdHandler->addCommand(
                        array($command, $command->action), $regex
                    );    
                }
            } else {
                $cmdHandler->addCommand(
                    array($command, $command->action),
                    $command->regex
                );      
            }      
        }
    }

}

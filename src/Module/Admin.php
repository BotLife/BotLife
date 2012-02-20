<?php

namespace Botlife\Module;

class Admin extends AModule
{

    public $commands = array(
        '\Botlife\Command\Spamfilter',
        '\Botlife\Command\Admin\Command',
    );

}

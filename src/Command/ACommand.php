<?php

namespace Botlife\Command;

abstract class ACommand
{

    public $needsSpamfilter = true;
    public $needsAuth       = false;

}

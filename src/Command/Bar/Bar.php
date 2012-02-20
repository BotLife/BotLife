<?php

namespace Botlife\Command\Bar;

class Bar extends \Botlife\Command\ACommand
{

    public $regex     = array(
        '/^[.!]bar$/i',
    );
    public $action    = 'run';
    public $needsAuth = true;
    
    public function run($event)
    {
        if (!$event->auth) {
            \Ircbot\Notice(
                $event->mask->nickname,
                'In order to you bar you need to be logged in to NickServ'
            );
            return;
        }
        $bar = \Botlife\Application\Storage::loadData('bar');
        if (!isset($bar->users)) {
            $bar->users = array();
        }
        if (!isset($bar->users[strtolower($event->auth)])) {
            \Ircbot\msg('#BotLife.Team', 'New bar user named: ' . $event->auth);
            $user = new \StorageObject;
            $user->bars       = 0;
            $user->lastPlayed = 0;
            $user->waitTime   = 0;
        } else {
            $user = $bar->users[strtolower($event->auth)];
        }
        if (($user->lastPlayed + $user->waitTime) > time()) {
            $waitTime = ($user->lastPlayed + $user->waitTime) - time();
            \Ircbot\Notice(
                $event->mask->nickname,
                'You still need to wait ' . $waitTime . ' seconds before you '
                    . 'can use bar again'
            );
            return;
        }
        $bars = round(rand(1, 56) * 10.3, 0);
        $user->bars = $user->bars + $bars;
        $user->lastPlayed = time();
        $user->waitTime   = round(rand(1,56) * 70.7, 0);
        \Ircbot\Notice(
            $event->mask->nickname,
            'You did get ' . $bars . ' bars. You now have ' . $user->bars
                . ' bars.'
        );
        var_dump($user);
        $bar->users[strtolower($event->auth)] = $user;
        \Botlife\Application\Storage::saveData('bar', $bar);
    }

}

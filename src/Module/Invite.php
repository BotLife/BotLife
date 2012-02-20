<?php

namespace Botlife\Module;

class Invite extends AModule
{

    public $events = array(
        'onInvite',
        'channelReady',
    );

    public function onInvite($event)
    {
        $invites = \Botlife\Application\Storage::loadData('invites');
        $hash    = md5($event->channel); 
        $invites->$hash = $event;
        \Ircbot\joinChan($event->channel);
        \Botlife\Application\Storage::saveData('invites', $invites);
    }
    
    public function channelReady($event)
    {
        $invites = \Botlife\Application\Storage::loadData('invites');
        $hash    = md5(\Ircbot\token('0'));
        if (!isset($invites->$hash)) {
            return;
        }
        $invite  = $invites->$hash;
        $channel = \Ircbot\Application::getInstance()->getChannelHandler()
            ->getChan(\Ircbot\token('0'), $event->botId);
        if (!$channel->isOp($invite->mask->nickname)) {
            \Ircbot\partChan(
                \Ircbot\token('0'),
                'I was invited by non-op ' . $invite->mask->nickname
            );
            return;
        } 
    }

}

<?php

namespace Botlife\Command\Admin;

class Spamfilter extends \Botlife\Command\ACommand
{

    public $regex = array(
        '/^SPAMFILTER( )?(?P<option>ENABLE|DISABLE|STATUS|IGNORE|UNIGNORE)?/i'
    );
    
    public $action          = 'spamfilter';
    public $needsAuth       = true;
    public $needsSpamfilter = false;

    public function spamfilter($event)
    {   
        if (strtolower($event->target) != '#botlife.team') {
            return;
        }
        if (!in_array(strtolower($event->auth), array('marlinc', 'adrenaline'))) {
            return;
        }
        if (isset($event->matches['option'])) {
            $option = strtoupper($event->matches['option']);
        } else {
            $option = 'STATUS';
        }
        if ($option == 'DISABLE') {
            $data = \Botlife\Application\Storage::loadData('spamfilter');
            $data->enabled = false;
            \Botlife\Application\Storage::saveData('spamfilter', $data);
            \Ircbot\msg('#BotLife.Team', 'The spamfilter is now disabled!');
        } elseif ($option == 'ENABLE') {
            $data = \Botlife\Application\Storage::loadData('spamfilter');
            $data->enabled = true;
            \Botlife\Application\Storage::saveData('spamfilter', $data);
            \Ircbot\msg('#BotLife.Team', 'The spamfilter is now enabled!');
        } elseif ($option == 'STATUS') {
            $data = \Botlife\Application\Storage::loadData('spamfilter');
            $heaviest = array();
            $tmp = $data->filter->host;
            arsort($tmp);
            $heaviest[0][0] = key($tmp);
            $heaviest[0][1] = current($tmp);
            $tmp = $data->filter->channel;
            arsort($tmp);
            $heaviest[1][0] = key($tmp);
            $heaviest[1][1] = current($tmp);
            $msg = 'Enabled: %s; Heaviest host: %s (%d); Heaviest channel: %s (%d)';
            $msg = sprintf(
                $msg,
                ($data->enabled) ? 'yes' : 'no',
                $heaviest[0][0], $heaviest[0][1],
                $heaviest[1][0], $heaviest[1][1]
            );
            \Ircbot\msg('#BotLife.Team', $msg);
        } elseif ($option == 'IGNORE') {
            if (!\Ircbot\token(2)) {
                \Ircbot\msg('#BotLife.Team', 'Please specify a mask');
                return;
            }
            $data = \Botlife\Application\Storage::loadData('spamfilter');
            $data->ignore[] = \Ircbot\token(2);
            \Botlife\Application\Storage::saveData('spamfilter', $data);
            \Ircbot\msg(
                '#BotLife.Team', 'Now ignoring mask: ' . \Ircbot\token(2)
            );
        } elseif ($option == 'UNIGNORE') {
            if (!\Ircbot\token(2)) {
                \Ircbot\msg('#BotLife.Team', 'Please specify a mask');
                return;
            }
            $data = \Botlife\Application\Storage::loadData('spamfilter');
            foreach ($data->ignore as $key => $pattern) {
                if ($pattern == \Ircbot\token(2)) {
                    unset($data->ignore[$key]);
                }
            }
            \Botlife\Application\Storage::saveData('spamfilter', $data);
            \Ircbot\msg(
                '#BotLife.Team', 'Nolonger ignoring mask: ' . \Ircbot\token(2)
            );
        } else {
            \Ircbot\msg('#BotLife.Team', 'Mmm unknown option ' . $option);
        }
    }
    
}

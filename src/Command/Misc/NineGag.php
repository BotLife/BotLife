<?php

namespace Botlife\Command\Misc;

class NineGag extends \Botlife\Command\ACommand
{

    public $regex  = array(
        '/^[.!@]9(gag)?$/i'
    );
    public $action = 'run';
    public $code   = '9gag';
    
    public function run($event)
    {
        $this->detectResponseType($event->message);
        
        $posts = $this->getPosts();
        $c = new \Botlife\Application\Colors;
        $this->respondWithInformation(array(
            'Title' => $posts[0]->title,
            'Link'  => $posts[0]->link,
        ));
    }
    
    public function getPosts()
    {
        $xml = simplexml_load_file('http://tumblr.9gag.com/rss');
        $post = new \StdClass;
        $tmp = array();
        foreach ($xml->channel->item as $item) {
            $tmp[] = $item;
        }
        usort($tmp, array($this, 'sort'));
        return $tmp;
    }
    
    public function sort($objectOne, $objectTwo)
    {
        if (strtotime($objectOne->pubDate) > strtotime($objectTwo->pubDate)) {
            return -1;
        } elseif (strtotime($objectOne->pubDate) == strtotime($objectTwo->pubDate)) {
            return 0;
        } elseif (strtotime($objectOne->pubDate) < strtotime($objectTwo->pubDate)) {
            return 1;
        }
    }

}

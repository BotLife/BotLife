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
        $this->detectResponseType($event->message, $event->target);
        
        $posts = $this->getPosts();
        if (count($posts) === 0) {
            $debug = new \Botlife\Debug;
            $debug->log('NineGag', 'Error', 'An error occured when fetching the 9gag data.');
            $this->respondWithInformation(array(
                'Error' => "Failed to fetch 9gag data."
            ));
            return;
        }
        $c = new \Botlife\Application\Colors;
        $this->respondWithInformation(array(
            'Title' => $posts[0]->title,
            'Link'  => $posts[0]->link,
        ));
    }
    
    public function getPosts()
    {
        $xml = @simplexml_load_file('http://tumblr.9gag.com/rss');
        if ($xml === false) {
            return array();
        }
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

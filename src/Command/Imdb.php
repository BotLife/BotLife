<?php

namespace Botlife\Command;

class Imdb extends ACommand
{
    
    public $code   = 'imdb';
    public $action = 'init';
    public $regex  = '/[.!@]imdb( )?(?P<title>\w+)?/i';
    
    public function init(\Ircbot\Type\MessageCommand $event)
    {
        $this->detectResponseType($event->message);
        if (!isset($event->matches['title'])) {
            $this->respondWithPrefix(
                'You might want to specifiy a movie title. '
                	. 'Example: !imdb 2012'
            );
            return false;
        }
        $dao  = new \Botlife\Dao\MovieInfo;
        $movie = $dao->getVideoInfo($event->matches['title']);
        if (!$movie) {
            $this->respondWithPrefix(
                'Could not find information related to that movie.'
            );
            return false;
        }
        $this->run($movie);
    }
    
    public function run($data)
    {
        $c    = new \BotLife\Application\Colors;
        $this->respondWithInformation(array(
        	'Title' => $data->title . $c(12, '[')
                . $c(3, gmdate('H:i:s', $data->duration)) . $c(12, ']'),
            'Rating' => array(
                $this->_getRatingBar($data->ratingAverage),
                array(
                    'Likes'    => number_format($data->ratingLikes),
                    'Dislikes' => number_format($data->ratingDislikes),
                ),
            ),
            'Released' => array(
                $data->released->format('Y-m-d'),
            ),
            'Plot' => $data->plot,
            'URL'  => $data->url,
        ));
    }
    
    private function _getRatingBar($ratings)
    {
        $ratings = round($ratings, 0);
        $str = null;
        $str .= chr(3) . '03' . str_repeat('★', $ratings);
        $str .= chr(3) . '12' . str_repeat('★', 5 - $ratings);
        return $str;
    }
    
}
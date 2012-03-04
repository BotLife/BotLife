<?php

namespace Botlife\Command;

class YouTube extends ACommand
{

    public $regex = array(
        '/(http\:\/\/)?(www\.)?(youtube\.com\/watch\?(.*)?v\=(?P<idlong>[A-Za-z0-9_-]+)(\&(.*))?|youtu\.be\/(?P<id>[A-Za-z0-9_-]+))/',
    );
    public $action = 'lookup';
    public $code   = 'youtube';
    
    public $responseType    = self::RESPONSE_PUBLIC;
    
    private $_lastrun;
    
    public function lookup($event)
    {
        if ((time() - $this->_lastrun) <= 3) {
            return;
        }
        $this->_lastrun = time();
        $videoId = (empty($event->matches['id'])) ? $event->matches['idlong'] : $event->matches['id'];
        $data = $this->getData($videoId);
        $C = new \BotLife\Application\Colors;
        
        $this->respondWithInformation(array(
            'Title'     => $data->title . $C(12, '[')
                . $C(3, gmdate('H:i:s', $data->duration)) . $C(12, ']'),
            'Rating'    => array(
                $this->_getRatingBar($data->ratingAverage),
                array(
                    'Likes'    => number_format($data->ratingLikes),
                    'Dislikes' => number_format($data->ratingDislikes),
                ),
            ),
            'Uploaded'  => array(
                $data->uploaded->format('Y-m-d'),
                array(
                    $data->uploader,
                ),
            ),
            'Favorites' => number_format($data->timesFavorited),
            'Views'     => number_format($data->views)
        ),  $C(12, 'You') . $C(03, 'Tube'));
    }
    
    public function getData($videoId)
    {
        $dOM = new \DOMDocument();
        @$dOM->load('https://gdata.youtube.com/feeds/api/videos/' . $videoId . '?v=2');
        $video = new \StdClass;
        $video->title = $dOM->getElementsByTagName('title')->item(0)->nodeValue;
        $video->uploader = $dOM->getElementsByTagName('author')->item(0)
            ->getElementsByTagName('name')->item(0)->nodeValue;
        $group = $dOM->getElementsByTagName('group')->item(0);
        $video->uploaded = new \DateTime($dOM->getElementsByTagName('uploaded')->item(0)
            ->nodeValue);
        $video->duration = (int) $dOM->getElementsByTagName('duration')->item(0)
            ->getAttribute('seconds');
        $rating = $dOM->getElementsByTagName('rating')->item(0);
        $video->ratingAverage = (float) $rating->getAttribute('average');
        $video->ratingTotal = (int) $rating->getAttribute('numRaters');
        $rating = $dOM->getElementsByTagName('rating')->item(1);
        $video->ratingLikes = (int) $rating->getAttribute('numLikes');
        $video->ratingDislikes = (int) $rating->getAttribute('numDislikes');
        $statistics = $dOM->getElementsByTagName('statistics')->item(0);
        $video->views = (int) $statistics->getAttribute('viewCount');
        $video->timesFavorited = (int) $statistics->getAttribute('favoriteCount');
        return $video;
    }
    
    private function _getRatingBar($ratings) {
        $ratings = round($ratings, 0);
        $str = null;
        $str .= chr(3) . '03' . str_repeat('★', $ratings);
        $str .= chr(3) . '12' . str_repeat('★', 5 - $ratings);
        return $str;
    }
    
}

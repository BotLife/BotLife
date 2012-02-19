<?php

namespace Botlife\Command;

class YouTube extends ACommand
{

    public $regex = array(
        '/(http\:\/\/)?(www\.)?(youtube\.com\/watch\?(.*)?v\=(?P<idlong>[A-Za-z0-9_-]+)(\&(.*))?|youtu\.be\/(?P<id>[A-Za-z0-9_-]+))/',
    );
    public $action = 'lookup';

    public function lookup($event)
    {
        $videoId = (empty($event->matches['id'])) ? $event->matches['idlong'] : $event->matches['id'];
        $data = $this->getData($videoId);
        $C = new \BotLife\Application\Colors;
        $response = $C(12, '[') . $C(1, 'You') . $C(4, 'Tube') . $C(12, '] ');
        $response .= $C(12, 'Title: ') . $C(3, $data->title);
        $response .= $C(12, '[') . $C(3, gmdate('H:i:s', $data->duration))
            . $C(12, ']') . ' - ';
        $response .= $C(12, 'Rating: ')
            . $C(3, $this->_getRatingBar($data->ratingAverage));
        $response .= $C(12, '(Likes: ') . $C(3, number_format($data->ratingLikes));
        $response .= $C(12, '/Dislikes: ') . $C(3, number_format($data->ratingDislikes));
        $response .= $C(12, ') - Uploaded: ') . $C(3, $data->uploaded->format('Y-m-d'));
        $response .= $C(12, '(') . $C(3, $data->uploader) . $C(12, ') - ');
        $response .= $C(12, 'Favorites: ') . $C(3, number_format($data->timesFavorited));
        $response .= $C(12, ' - Views: ') . $C(3, number_format($data->views));
        \Ircbot\notice($event->mask->nickname, $response);
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
        $str .= chr(3) . '08' . str_repeat('★', $ratings);
        $str .= chr(3) . '01' . str_repeat('★', 5 - $ratings);
        return $str;
    }
    
}

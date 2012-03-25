<?php

namespace Botlife\Dao;

class MovieInfo
{
    
    public function getVideoInfo($title)
    {
        $data = $this->_getData($title);
        
        if (!isset($data->Title)) {
            return false;
        }
        
        var_dump($data);
        
        $movie = new \StdClass;
        $movie->title = $data->Title;
        if ($data->Released != 'N/A') {
            $movie->released = new \DateTime($data->Released);
        } else {
            $movie->released = null;
        }
        $movie->duration = $this->_getDuration($data->Runtime);
        $movie->ratingAverage = round((float) $data->Rating / 2);
        $movie->ratingTotal = (int) $data->Votes;
        list($movie->ratingLikes, $movie->ratingDislikes) = $this
            ->_splitRating($data->Rating, $movie->ratingTotal);
        
        $movie->plot = $data->Plot;
        $movie->url = 'http://www.imdb.com/title/' . $data->ID . '/';
        
        return $movie;
    }
    
    private function _getData($title)
    {
        return json_decode(file_get_contents(
        	'http://www.imdbapi.com/?i=&t=' . urlencode($title)
        ));
    }
    
    private function _getDuration($text)
    {
        $pattern = '';
        $pattern .= '/^';
        $pattern .= '((?P<hours>\d+) hr(s)?( )?)?';
        $pattern .= '((?P<mins>\d+) min(s)?( )?)?';
        $pattern .= '$/';
        preg_match($pattern, $text, $matches);
        $duration = 0;
        if (isset($matches['hours'])) {
            $duration += 3600 * (int) $matches['hours'];
        }
        if (isset($matches['mins'])) {
            $duration += 60 * (int) $matches['mins'];
        }
        return $duration;
    }
    
    private function _splitRating($average, $amount)
    {
        $like = (float) $average * 10;
        $dislike = (100 - $like);
        $like *= $amount / 100;
        $dislike *= $amount / 100;
        return array($like, $dislike);
    }
    
}

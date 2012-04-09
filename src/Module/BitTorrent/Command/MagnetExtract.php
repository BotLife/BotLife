<?php

namespace Botlife\Module\BitTorrent\Command;

use Ircbot\Type\MessageCommand;

class MagnetExtract extends \BotLife\Command\ACommand
{

    public $regex  = '/^magnet\:.*/i';
    public $action = 'run';
    public $code   = 'magnet';
    
    public $responseType    = self::RESPONSE_PUBLIC;
    
    public function run(MessageCommand $event)
    {
        $this->detectResponseType($event->message, $event->target);
        $parser = new \MagnetUri($event->message);
        $math   = new \Botlife\Utility\Math;
        $math->units = array('kb', 'mb', 'gb', 'tb');
        if (!$parser->isValid()) {
            return;
        }
        $data = array();
        if ($parser->dn) {
            $data['Display name'] = array($parser->dn);
            if ($parser->xl) {
                $data['Display name'][]['eXact Length']
                    = $math->alphaRound($parser->xl);
            }
        }
        if ($parser->xt) {
            $data['eXact Topic'] = $parser->xt;
        }
        if ($parser->as) {
            $data['Acceptable Source'] = $parser->as;
        }
        if ($parser->tr) {
            $data['address TRacker'] = $parser->tr;    
        }
        if (empty($data)) {
            return;
        }
        $this->respondWithInformation($data);
    }

}
    
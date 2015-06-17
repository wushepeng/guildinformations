<?php

namespace App\Resource;

use App\AbstractResource;
use App\Entity\Guild;

/**
 * Class Resource
 * @package App
 */
class GuildResource extends AbstractResource {

    public function post() {
        
    }

    private function convertToArray(Guild $guild) {
        return array(
            'id' => $guild->getId(),
            'name' => $guild->getName(),
            'apiKey' => $guild->getApiKey(),
            'mainGuildId' => $guild->getMainGuildId()
        );
    }
}
?>
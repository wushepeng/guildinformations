<?php

namespace App\Resource;

use App\AbstractResource;
use App\Entity\Homin;

/**
 * Class Resource
 * @package App
 */
class HominResource extends AbstractResource {

    public function post() {
        /*$homin = new Homin($id, $name, $apiKey, $guildId);
        $this->getEntityManager()->persist($homin);
        $this->getEntityManager()->flush();
        return convertToArray($homin);
        $homin = $this->getEntityManager()->find('App\Entity\Homin', array("id" => $id));
        $this->getEntityManager()->remove($homin);*/
    }

    private function convertToArray(Homin $homin) {
        return array(
            'id' => $homin->getId(),
            'name' => $homin->getName(),
            'apiKey' => $homin->getApiKey(),
            'guildId' => $homin->getGuildId()
        );
    }
}
?>
<?php

namespace App\Resource;

use App\AbstractResource;
use App\Entity\Homin;

/**
 * Class Resource
 * @package App
 */
class HominResource extends AbstractResource {

    private function post($id, $name, $apiKey, $guildId) {
        $homin = new Homin($id, $name, $apiKey, $guildId);
        $this->getEntityManager()->persist($homin);
        $this->getEntityManager()->flush();
    }

    public function get($id) {
        $homin = $this->getEntityManager()->find('App\Entity\Homin', array("id" => $id));
        if($homin==null) {
            return null;
        }
        else {
            return $this->convertToArray($homin);
        }
    }

    public function put($id, $name, $apiKey, $guildId) {
        $homin = $this->getEntityManager()->find('App\Entity\Homin', array("id" => $id));
        if($homin==null) {
            $this->post($id, $name, $apiKey, $guildId);
            return;
        }
        $homin->setName($name);
        if($apiKey!=null) {
            $homin->setApiKey($apiKey);
        }
        $homin->setGuildId($guildId);
        $this->getEntityManager()->flush();
    }

    public function delete($id) {
        $homin = $this->getEntityManager()->find('App\Entity\Homin', array("id" => $id));
        $this->getEntityManager()->remove($homin);
        $this->getEntityManager()->flush();
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
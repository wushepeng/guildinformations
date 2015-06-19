<?php

namespace App\Resource;

use App\AbstractResource;
use App\Entity\Guild;

/**
 * Class Resource
 * @package App
 */
class GuildResource extends AbstractResource {

    public function post($id, $name, $apiKey, $mainGuildId) {
        $guild = new Guild($id, $name, $apiKey, $mainGuildId);
        $this->getEntityManager()->persist($guild);
        $this->getEntityManager()->flush();
    }

    public function get($id) {
        $guild = $this->getEntityManager()->find('App\Entity\Guild', array("id" => $id));
        if($guild==null) {
            return null;
        }
        else {
            return $this->convertToArray($guild);
        }
    }

    public function put($id, $name, $apiKey, $mainGuildId) {
        $guild = $this->getEntityManager()->find('App\Entity\Guild', array("id" => $id));
        $guild->setName($name);
        if($apiKey!=null) {
            $guild->setApiKey($apiKey);
        }
        $guild->setMainGuildId($mainGuildId);
        $this->getEntityManager()->flush();
    }

    public function delete($id) {
        $guild = $this->getEntityManager()->find('App\Entity\Guild', array("id" => $id));
        $this->getEntityManager()->remove($guild);
        $this->getEntityManager()->flush();
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
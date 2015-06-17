<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class GuildRepository extends EntityRepository {

    public function getGuilds() {
        $dql = "SELECT g.id, g.name FROM App\Entity\Guild g";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
?>
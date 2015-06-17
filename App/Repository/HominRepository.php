<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class HominRepository extends EntityRepository {

    public function getHomins($guildId) {
        $dql = "SELECT h.id, h.name FROM App\Entity\Homin h WHERE h.guildId = :guildId";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('guildId', $guildId);
        return $query->getResult();
    }
}
?>
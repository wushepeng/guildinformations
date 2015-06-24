<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class HominRepository extends EntityRepository {

    public function getGuildMemberKeys($guildId) {
        $dql = "SELECT h.name, h.apiKey FROM App\Entity\Homin h WHERE h.guildId = :guildId ORDER BY h.name";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('guildId', $guildId);
        return $query->getResult();
    }
}
?>
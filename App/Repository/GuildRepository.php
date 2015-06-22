<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class GuildRepository extends EntityRepository {

    public function getRelatedGuilds($guildId) {
        $dql = "SELECT g.id, g.name, g.mainGuildId, g.apiKey FROM App\Entity\Guild g WHERE g.id != g.mainGuildId AND g.mainGuildId = :guildId";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('guildId', $guildId);
        return $query->getResult();
    }
}
?>
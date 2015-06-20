<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class GeneralConfigRepository extends EntityRepository {

    public function getGeneralConfig() {
        $dql = "SELECT g.appKey, g.appUrl, g.appMaxAge FROM App\Entity\GeneralConfig g";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults(1);
        return $query->getSingleResult();
    }
}
?>
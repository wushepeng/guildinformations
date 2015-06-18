<?php

namespace App\Repository;

use Doctrine\ORM\GeneralConfigRepository;

class GeneralConfigRepository extends EntityRepository {

    public function getGeneralConfig() {
        $dql = "SELECT g.appKey, g.appUrl, g.appMaxAge FROM App\Entity\GeneralConfig g";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
}
?>
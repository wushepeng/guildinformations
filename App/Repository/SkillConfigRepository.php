<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class SkillConfigRepository extends EntityRepository {

    public function getSkillConfig($hominId) {
        $dql = "SELECT s.skillCode, s.visible FROM App\Entity\SkillConfig s WHERE s.hominId = :hominId";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('hominId', $hominId);
        return $query->getResult();
    }
}
?>
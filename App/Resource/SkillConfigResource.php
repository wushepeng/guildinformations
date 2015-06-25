<?php

namespace App\Resource;

use App\AbstractResource;
use App\Entity\SkillConfig;

/**
 * Class Resource
 * @package App
 */
class SkillConfigResource extends AbstractResource {

    public function post($hominId, $skillCode, $visible) {
        $skillConf = new SkillConfig($hominId, $skillCode, $visible);
        $this->getEntityManager()->persist($skillConf);
        $this->getEntityManager()->flush();
    }

    public function get($hominId, $skillCode) {
        $skillConf = $this->getEntityManager()->find('App\Entity\SkillConfig', array("hominId" => $hominId, "skillCode" => $skillCode));
        if($skillConf==null) {
            return null;
        }
        else {
            return $this->convertToArray($skillConf);
        }
    }

    public function put($hominId, $skillCode, $visible) {
        $skillConf = $this->getEntityManager()->find('App\Entity\SkillConfig', array("hominId" => $hominId, "skillCode" => $skillCode));
        $skillConf->setVisible($visible);
        $this->getEntityManager()->flush();
    }

    private function convertToArray(SkillConfig $skillConfig) {
        return array(
            'hominId' => $skillConfig->getHominId(),
            'skillCode' => $skillConfig->getSkillCode(),
            'visible' => $skillConfig->getVisible()
        );
    }
}
?>
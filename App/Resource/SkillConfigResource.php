<?php

namespace App\Resource;

use App\AbstractResource;
use App\Entity\SkillConfig;

/**
 * Class Resource
 * @package App
 */
class SkillConfigResource extends AbstractResource {

    public function post() {
        
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
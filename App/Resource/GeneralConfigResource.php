<?php

namespace App\Resource;

use App\AbstractResource;
use App\Entity\GeneralConfig;

/**
 * Class Resource
 * @package App
 */
class GeneralConfigResource extends AbstractResource {

    public function post() {
        
    }

    private function convertToArray(GeneralConfig $generalConfig) {
        return array(
            'appKey' => $generalConfig->getAppKey(),
            'appUrl' => $generalConfig->getAppUrl(),
            'appMaxAge' => $generalConfig->getAppMaxAge()
        );
    }
}
?>
<?php

namespace App\TwigExtension;

class GuildInfTwigExtension extends \Twig_Extension {
    public function getName() {
        return 'guildInf';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('getColor', array($this, 'getColor')),
        );
    }

    public function getColor($level) {
        $color = "#";
        if($level < 25) {
            $color .= "ff0000";
        }
        else if($level >= 25 && $level < 50) {
            $color .= "ff2200";
        }
        else if($level >= 50 && $level < 75) {
            $color .= "cc2200";
        }
        else if($level >= 75 && $level < 100) {
            $color .= "cc4400";
        }
        else if($level >= 100 && $level < 125) {
            $color .= "aa4400";
        }
        else if($level >= 125 && $level < 150) {
            $color .= "aa6600";
        }
        else if($level >= 150 && $level < 175) {
            $color .= "886600";
        }
        else if($level >= 175 && $level < 200) {
            $color .= "668800";
        }
        else if($level >= 200 && $level < 225) {
            $color .= "44aa00";
        }
        else if($level >= 225 && $level < 250) {
            $color .= "22cc00";
        }
        else if($level == 250) {
            $color .= "00ff00";
        }
        return $color;
    }
}

?>
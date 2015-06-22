<?php
require '../vendor/autoload.php';
require '../libs/ryapi.php';
require '../libs/ryzom_extra.php';

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

$generalConfigResource = new \App\Resource\GeneralConfigResource();
$guildResource = new \App\Resource\GuildResource();
$hominResource = new \App\Resource\HominResource();
$skillConfigResource = new \App\Resource\SkillConfigResource();

$generalConf = $generalConfigResource->getEntityManager()->getRepository('\App\Entity\GeneralConfig')-> getGeneralConfig();
define('RYAPI_APP_KEY',$generalConf['appKey']);
define('RYAPI_APP_URL',$generalConf['appUrl']);
define('RYAPI_APP_MAXAGE',$generalConf['appMaxAge']);

ryapi_init();

include_once "../App/Router.php";

// Run app
$app->run();
?>
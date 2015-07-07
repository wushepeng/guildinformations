<?php
require '../vendor/autoload.php';
require '../libs/ryapi.php';
require '../libs/ryzom_extra.php';

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
    'log.enabled' => true,
    'log.level' => \Slim\Log::INFO
));

// Prepare logger
$app->getLog()->setWriter(new \App\Logger());

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(
	new \Slim\Views\TwigExtension(),
	new \Dkesberg\Slim\Twig\Extension\TranslationExtension()
);

// Prepare translator
$app->container->singleton('translator', function() {
    return new \Illuminate\Translation\Translator(new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem(), __DIR__.'/../lang'), 'fr');
});
$app->translator->setFallback('fr');

$generalConfigResource = new \App\Resource\GeneralConfigResource();
$guildResource = new \App\Resource\GuildResource();
$hominResource = new \App\Resource\HominResource();

$generalConf = $generalConfigResource->getEntityManager()->getRepository('\App\Entity\GeneralConfig')-> getGeneralConfig();
define('RYAPI_APP_KEY',$generalConf['appKey']);
define('RYAPI_APP_URL',$generalConf['appUrl']);
define('RYAPI_APP_MAXAGE',$generalConf['appMaxAge']);

ryapi_init();

include_once "../App/Router.php";

// Run app
$app->run();
?>
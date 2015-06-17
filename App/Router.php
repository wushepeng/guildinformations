<?php

/*
 * Page d'accueil
 */
$app->get('/', function() use ($app) {
	echo $app->view->render("home.html.twig");
})->name('home');

/*
 * Stuff
 */
/*$app->get('/a/route(/)', function() use ($app) {
$app->get('/a/route/with/:aParam(/)', function($aParam) use ($app, $entityResource) {
	$app->request()->params('paramName');
	$entityResource->getEntityManager()->getRepository('App\Entity\anEntity')->function();
	$app->redirect($app->urlFor('routeName'));
	$data = array(
		'x' => $x,
		'y' => $y
	);
	echo $app->view->render("aTemplate.html.twig", $data);
})->name('aName');*/

?>
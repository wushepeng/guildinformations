<?php

/*
 * Page d'accueil
 */
$app->get('/', function() use ($app) {
	echo $app->view->render("home.html.twig");
})->name('home');

/*
 * Route appelée par Ryzom
 */
$app->get('/ryzom/app(/)', function() use ($app) {

})->name('ryzomApp-Home');

/*
 * Affichage des inventaires
 */
$app->get('/ryzom/app/inventory(/)', function() use ($app) {

})->name('ryzomApp-Inventory');

/*
 * Stuff
 */
/*$app->post('/a/route(/)', function() use ($app) {
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
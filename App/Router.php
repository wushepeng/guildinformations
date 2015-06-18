<?php

/*
 * Page d'accueil du site
 */
$app->get('/', function() use ($app) {
	echo $app->view->render("home.html.twig");
})->name('home');

/*
 * Route appelée par Ryzom
 */
$app->get('/ryzom/app(/)', function() use ($app) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$hashmac = hash_hmac('sha1', $user, RYAPI_APP_KEY);
	if($hashmac!=$checksum) {
		$data = array('errorText' => "Erreur de somme de contrôle");
		echo $app->view->render("error.html.twig", $data);
	}
	else {
		$userData = unserialize(base64_decode($user));
	}
})->name('ryzomApp-Home');

/*
 * Affichage des inventaires
 */
$app->get('/ryzom/app/inventory(/)', function() use ($app) {

})->name('ryzomApp-Inventory');

/*
 * Création/mise à jour de la clé api d'un homin
 */
$app->post('/ryzom/app/homin/apiKey(/)', function() use ($app) {

})->name('ryzomApp-HominKey');

/*
 * Création/mise à jour de la clé api d'une guilde
 */
$app->post('/ryzom/app/guild/apiKey(/)', function() use ($app) {

})->name('ryzomApp-GuildKey');

/*
 * Création/mise à jour de la configuration pour l'affichage des compétences
 */
$app->post('/ryzom/app/homin/configuration(/)', function() use ($app) {

})->name('ryzomApp-HominConfiguration');

/*
 * Création/mise à jour de la configuration d'une guilde
 */
$app->post('/ryzom/app/guild/configuration(/)', function() use ($app) {

})->name('ryzomApp-GuildConfiguration');

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
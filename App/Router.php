<?php

function checkRequest(\Slim\Route $route) {
	$app = \Slim\Slim::getInstance();
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	if($user==null || $checksum==null) {
		$message = "L'application ne marche pas sur appels directs, veuillez passer par ryzom (ig ou webapp)";
		$app->redirect($app->urlFor('ryzomApp-Error', array('message' => urlencode($message))));
	}
	else {
		$hashmac = hash_hmac('sha1', $user, "RYAPI_APP_KEY");
		if($hashmac!=$checksum) {
			$message = "Erreur de somme de contrôle";
			$app->redirect($app->urlFor('ryzomApp-Error', array('message' => urlencode($message))));
		}
	}
}

/*
 * Page d'accueil du site
 */
$app->get('/', function() use ($app) {
	echo $app->view->render("home.html.twig");
})->name('home');

/*
 * Route appelée par Ryzom
 */
$app->get('/ryzom/app(/)', 'checkRequest', function() use ($app) {
	$user = $app->request()->params('user');
	$userData = unserialize(base64_decode($user));
	$hominId = $userData['id'];
	$hominName = $userData['char_name'];
	$guildId = $userData['guild_id'];
	$guildName = $userData['guild_name'];
	$grade = $userData['grade'];
	// index en plus: timestamp, app_url, race, civilisation, cult, civ, organization, guild_icon, lang
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/home.ig.html.twig");
	}
	else {
		echo $app->view->render("home.app.html.twig");
	}
})->name('ryzomApp-Home');

/*
 * Affichage des inventaires
 */
$app->get('/ryzom/app/inventory(/)', 'checkRequest', function() use ($app) {
	
})->name('ryzomApp-Inventory');

/*
 * Création/mise à jour de la clé api d'un homin
 */
$app->post('/ryzom/app/homin/apiKey(/)', 'checkRequest', function() use ($app) {

})->name('ryzomApp-HominKey');

/*
 * Création/mise à jour de la clé api d'une guilde
 */
$app->post('/ryzom/app/guild/apiKey(/)', 'checkRequest', function() use ($app) {

})->name('ryzomApp-GuildKey');

/*
 * Création/mise à jour de la configuration pour l'affichage des compétences
 */
$app->post('/ryzom/app/homin/configuration(/)', 'checkRequest', function() use ($app) {

})->name('ryzomApp-HominConfiguration');

/*
 * Création/mise à jour de la configuration d'une guilde
 */
$app->post('/ryzom/app/guild/configuration(/)', 'checkRequest', function() use ($app) {

})->name('ryzomApp-GuildConfiguration');

/*
 * Route d'affichage des erreurs
 */
$app->get('/ryzom/app/error/:message(/)', function($message) use ($app) {
	$data = array('errorText' => urldecode($message));
	echo $app->view->render("error.html.twig", $data);
})->name('ryzomApp-Error');

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
<?php

include 'Utilities.php';

function checkRequest(\Slim\Route $route) {
	$app = \Slim\Slim::getInstance();
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	if($user==null || $checksum==null) {
		$message = "L'application ne marche pas sur appels directs, veuillez passer par ryzom (ig ou webapp)";
		$app->redirect($app->urlFor('ryzomApp-Error', array('message' => urlencode($message))));
	}
	else {
		$hashmac = hash_hmac('sha1', $user, RYAPI_APP_KEY);
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
$app->get('/ryzom/app(/)', 'checkRequest', function() use ($app, $hominResource, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$hominId = $userData['id'];
	$hominName = $userData['char_name'];
	$guildId = $userData['guild_id'];
	$guildName = $userData['guild_name'];
	$grade = $userData['grade'];
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'grade' => $grade
	);
	// index en plus: timestamp, app_url, race, civilisation, cult, civ, organization, guild_icon, lang
	$homin = $hominResource->get($hominId);
	if($homin==null) {
		$hominResource->post($hominId, $hominName, null, $guildId);
	}
	else {
		$hominResource->put($hominId, $hominName, null, $guildId);
	}
	$guild = $guildResource->get($guildId);
	if($guild==null) {
		$guildResource->post($guildId, $guildName, null, $guildId);
	}
	else {
		$guildResource->put($guildId, $guildName, null, $guild['mainGuildId']);
	}
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/home.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("home.app.html.twig", $data);
	}
})->name('ryzomApp-Home');

/**
 * Affichage du formulaire pour la clé api d'un homin
 */
$app->get('/ryzom/app/homin/apiKey(/)', 'checkRequest', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$homin = $hominResource->get($userData['id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'apiKey' => $homin['apiKey']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/hominKey.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("hominKey.app.html.twig", $data);
	}
})->name('ryzomApp-HominKey');

/*
 * Création/mise à jour de la clé api d'un homin
 */
$app->post('/ryzom/app/homin/apiKey(/)', 'checkRequest', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$apiKey = $app->request()->params('apiKey');
	$userData = unserialize(base64_decode($user));
	$homin = $hominResource->put($userData['id'], $userData['char_name'], $apiKey, $userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'apiKey' => $apiKey
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/hominKey.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("hominKey.app.html.twig", $data);
	}
})->name('ryzomApp-HominKey.post');

/*
 * Affichage du formulaire pour la clé api d'une guilde
 */
$app->get('/ryzom/app/guild/apiKey(/)', 'checkRequest', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		//@TODO: que pour le leader de la guilde, rediriger
	}
	$guild = $guildResource->get($userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'apiKey' => $guild['apiKey']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/guildKey.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("guildKey.app.html.twig", $data);
	}
})->name('ryzomApp-GuildKey');

/*
 * Création/mise à jour de la clé api d'une guilde
 */
$app->post('/ryzom/app/guild/apiKey(/)', 'checkRequest', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$apiKey = $app->request()->params('apiKey');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		//@TODO: que pour le leader de la guilde, rediriger
	}
	$guild = $guildResource->put($userData['guild_id'], $userData['guild_name'], $apiKey, $userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'apiKey' => $apiKey
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/guildKey.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("guildKey.app.html.twig", $data);
	}
})->name('ryzomApp-GuildKey.post');

/*
 * Affichage de la configuration d'une guilde
 */
$app->get('/ryzom/app/guild/configuration(/)', 'checkRequest', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		//@TODO: que pour le leader de la guilde, rediriger
	}
	$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guilds' => $guilds
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/guildConf.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("guildConf.app.html.twig", $data);
	}
})->name('ryzomApp-GuildConfiguration');

/*
 * Création/mise à jour de la configuration d'une guilde
 */
$app->post('/ryzom/app/guild/configuration(/)', 'checkRequest', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$apiKey = $app->request()->params('newApiKey');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		//@TODO: que pour le leader de la guilde, rediriger
	}
	$xml = ryzom_guild_api($apiKey);
	$id = $xml[$apiKey]->gid;
	$name = $xml[$apiKey]->name;
	$guildResource->post($id, $name, $apiKey, $userData['guild_id']);
	$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guilds' => $guilds
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/guildConf.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("guildConf.app.html.twig", $data);
	}
})->name('ryzomApp-GuildConfiguration.post');

/*
 * Suppression d'une guilde secondaire
 */
$app->get('/ryzom/app/guild/configuration/:guildId(/)', 'checkRequest', function($guildId) use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		//@TODO: que pour le leader de la guilde, rediriger
	}
	$guildResource->delete($guildId);
	$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guilds' => $guilds
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/guildConf.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("guildConf.app.html.twig", $data);
	}
})->name('ryzomApp-GuildConfiguration.delete');

/*
 * Affichage des inventaires
 */
$app->get('/ryzom/app/inventory(/)', 'checkRequest', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildsItems = getItems($userData['guild_id'], $userData['grade']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guilds' => $guildsItems
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/inventory.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("inventory.app.html.twig", $data);
	}
})->name('ryzomApp-Inventory');

/*
 * Page d'accueil pour voir les compétences des membres
 */
$app->get('/ryzom/app/skills(/)', 'checkRequest', function() use ($app, $guildResource, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	// @TODO
	$data = array(
		'user' => $user,
		'checksum' => $checksum
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/skills.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("skills.app.html.twig", $data);
	}
})->name('ryzomApp-Skills');

/*
 * Affichage des compétences de forage
 */
$app->get('/ryzom/app/skills/harvest(/)', 'checkRequest', function() use ($app, $guildResource, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getHominLevels($homin['apiKey'], 'h');
		array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/harvest.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("harvest.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Harvest');

/*
 * Affichage des compétences d'artisanat
 */
$app->get('/ryzom/app/skills/craft(/)', 'checkRequest', function() use ($app, $guildResource, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	// @TODO
	$data = array(
		'user' => $user,
		'checksum' => $checksum
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft');

/*
 * Affichage des compétences de magie
 */
$app->get('/ryzom/app/skills/magic(/)', 'checkRequest', function() use ($app, $guildResource, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	// @TODO
	$data = array(
		'user' => $user,
		'checksum' => $checksum
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/magic.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("magic.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Magic');

/*
 * Affichage des compétences de combat
 */
$app->get('/ryzom/app/skills/fight(/)', 'checkRequest', function() use ($app, $guildResource, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	// @TODO
	$data = array(
		'user' => $user,
		'checksum' => $checksum
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/fight.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("fight.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Fight');

/*
 * Création/mise à jour de la configuration pour l'affichage des compétences
 */
$app->post('/ryzom/app/homin/configuration(/)', 'checkRequest', function() use ($app) {

})->name('ryzomApp-HominConfiguration');

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
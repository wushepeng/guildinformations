<?php

include 'Utilities.php';

function checkRequest(\Slim\Route $route) {
	$app = \Slim\Slim::getInstance();
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	if($user==null || $checksum==null) {
		$message = "L'application ne marche pas sur appels directs, veuillez passer par ryzom (ig ou webapp)";
		$log = "what:param user or checksum missing | path:".$app->request->getPathInfo();
		$app->log->warning($log);
		$app->redirect($app->urlFor('ryzomApp-Error', array('message' => urlencode($message))));
	}
	else {
		$hashmac = hash_hmac('sha1', $user, RYAPI_APP_KEY);
		if($hashmac!=$checksum) {
			$message = "Erreur de somme de contrôle";
			$log = "what:bad checksum | path:".$app->request->getPathInfo();
			$app->log->warning($log);
			$app->redirect($app->urlFor('ryzomApp-Error', array('message' => urlencode($message))));
		}
		$userData = unserialize(base64_decode($user));
		$log = "id:".$userData['id']." | ";
		$log .= "name:".$userData['char_name']." | ";
		$log .= "guild:".$userData['guild_id']." | ";
		$log .= "guildName:".$userData['guild_name']." | ";
		$ig = $app->request()->params('ig');
		if($ig!=null) {
			$log = "ig:".$ig." | ";
		}
		$log .= "path:".$app->request->getPathInfo();
		$app->log->info($log);
		$app->translator->setLocale($userData['lang']);
	}
}

function isGuilded(\Slim\Route $route) {
	$app = \Slim\Slim::getInstance();
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	if($userData['guild_id']==0) {
		$app->redirect($app->urlFor('ryzomApp-Home'));
	}
}

/*
 * Page d'accueil du site
 */
$app->get('/', function() use ($app) {
	echo $app->view->render("home.html.twig");
})->name('home');

/*
 * Route appelée par Ryzom, page d'accueil de l'application
 */
$app->get('/ryzom/app(/)', 'checkRequest', function() use ($app, $hominResource, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$hominId = $userData['id'];
	$hominName = $userData['char_name'];
	$guildId = $userData['guild_id'];
	$guildName = $userData['guild_id']!=0?$userData['guild_name']:"NoGuild";
	$grade = $userData['guild_id']!=0?$userData['grade']:"NoGuild";
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'grade' => $grade,
		'name' =>$userData['char_name']
	);
	// index en plus: timestamp, app_url, race, civilisation, cult, civ, organization, guild_icon, lang
	$hominResource->put($hominId, $hominName, null, $guildId);
	if($userData['guild_id']!=0) {
		$guild = $guildResource->get($guildId);
		if($guild==null) {
			$guildResource->post($guildId, $guildName, null, $guildId);
		}
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
$app->get('/ryzom/app/homin/apiKey(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$homin = $hominResource->get($userData['id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'apiKey' => $homin['apiKey'],
		'error' => $app->request()->params('error'),
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
$app->post('/ryzom/app/homin/apiKey(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$apiKey = $app->request()->params('apiKey');
	$userData = unserialize(base64_decode($user));
	$homin = $hominResource->put($userData['id'], $userData['char_name'], $apiKey, $userData['guild_id']);
	$app->redirect('/ryzom/app/homin/apiKey?checksum='.$checksum.'&user='.$user);
})->name('ryzomApp-HominKey.post');

/*
 * Affichage du formulaire pour la clé api d'une guilde
 */
$app->get('/ryzom/app/guild/apiKey(/)', 'checkRequest', 'isGuilded', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		$app->redirect('/ryzom/app?checksum='.$checksum.'&user='.$user);
	}
	$guild = $guildResource->get($userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'apiKey' => $guild['apiKey'],
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
$app->post('/ryzom/app/guild/apiKey(/)', 'checkRequest', 'isGuilded', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$apiKey = $app->request()->params('apiKey');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		$app->redirect('/ryzom/app?checksum='.$checksum.'&user='.$user);
	}
	$guild = $guildResource->put($userData['guild_id'], $userData['guild_name'], $apiKey, null);
	$app->redirect('/ryzom/app/guild/apiKey?checksum='.$checksum.'&user='.$user);
})->name('ryzomApp-GuildKey.post');

/*
 * Affichage de la configuration d'une guilde
 */
$app->get('/ryzom/app/guild/configuration(/)', 'checkRequest', 'isGuilded', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		$app->redirect('/ryzom/app?checksum='.$checksum.'&user='.$user);
	}
	$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guilds' => $guilds,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
$app->post('/ryzom/app/guild/configuration(/)', 'checkRequest', 'isGuilded', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$apiKey = $app->request()->params('newApiKey');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		$app->redirect('/ryzom/app?checksum='.$checksum.'&user='.$user);
	}
	$xml = ryzom_guild_api($apiKey);
	$error = false;
	if(!isset($xml[$apiKey]->gid) || !isset($xml[$apiKey]->name)) {
		$error = true;
	}
	else {
		$id = $xml[$apiKey]->gid;
		$name = $xml[$apiKey]->name;
		$guild = $guildResource->get($id);
		if($guild==null) {
			$guildResource->post($id, $name, $apiKey, $userData['guild_id']);
		}
		else {
			$guildResource->put($id, $name, $apiKey, $userData['guild_id']);
		}
	}
	$app->redirect('/ryzom/app/guild/configuration?checksum='.$checksum.'&user='.$user);
})->name('ryzomApp-GuildConfiguration.post');

/*
 * Suppression d'une guilde secondaire
 */
$app->get('/ryzom/app/guild/configuration/:guildId(/)', 'checkRequest', 'isGuilded', function($guildId) use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
		$app->redirect('/ryzom/app?checksum='.$checksum.'&user='.$user);
	}
	$guildResource->delete($guildId);
	$app->redirect('/ryzom/app/guild/configuration?checksum='.$checksum.'&user='.$user);
})->name('ryzomApp-GuildConfiguration.delete');

/*
 * Page d'accueil des inventaires
 */
$app->get('/ryzom/app/inventory(/)', 'checkRequest', 'isGuilded', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMenu = array();
	array_push($guildMenu, array('id' => $userData['guild_id'], 'name' => $userData['guild_name']));
	if($userData['grade']=="HighOfficer" || $userData['grade']=="Leader") {
		$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
		foreach($guilds as $guild) {
			array_push($guildMenu, $guild);
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guilds' => $guildMenu,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/inventoryHome.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("inventoryHome.app.html.twig", $data);
	}
})->name('ryzomApp-Inventory');

/*
 * Recherche d'un objet dans les inventaires
 */
$app->post('/ryzom/app/inventory(/)', 'checkRequest', 'isGuilded', function() use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$search = $app->request()->params('search');
	if($search==null || $search=="") {
		$app->redirect('/ryzom/app/inventory?checksum='.$checksum.'&user='.$user);
	}
	$searchResult = searchItem($search, $userData['guild_id'], $userData['grade']);
	$guildMenu = array();
	array_push($guildMenu, array('id' => $userData['guild_id'], 'name' => $userData['guild_name']));
	if($userData['grade']=="HighOfficer" || $userData['grade']=="Leader") {
		$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
		foreach($guilds as $guild) {
			array_push($guildMenu, $guild);
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guilds' => $guildMenu,
		'searchResult' => $searchResult,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/inventoryHome.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("inventoryHome.app.html.twig", $data);
	}
})->name('ryzomApp-Inventory.post');

/*
 * Affichage de l'inventaire d'une guilde
 */
$app->get('/ryzom/app/inventory/:guildId(/)', 'checkRequest', 'isGuilded', function($guildId) use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guild = $guildResource->get($guildId);
	if($guild==null) {
		$app->redirect('/ryzom/app/inventory?checksum='.$checksum.'&user='.$user);
	}
	$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
	if($userData['guild_id']!=$guildId) {
		if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
			$app->redirect('/ryzom/app/inventory?checksum='.$checksum.'&user='.$user);
		}
		$isRelated = false;
		foreach($guilds as $g) {
			if($g['id']==$guildId) {
				$isRelated = true;
				break;
			}
		}
		if(!$isRelated) {
			$app->redirect('/ryzom/app/inventory?checksum='.$checksum.'&user='.$user);
		}
	}
	$guildItems = getGuildItems($guild['apiKey']);
	if(!isset($guildItems['error'])) {
		usort($guildItems, 'sortByType');
	}
	$guildName = $guild['name'];
	$guildMenu = array();
	array_push($guildMenu, array('id' => $userData['guild_id'], 'name' => $userData['guild_name']));
	if($userData['grade']=="HighOfficer" || $userData['grade']=="Leader") {
		$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
		foreach($guilds as $guild) {
			array_push($guildMenu, $guild);
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guild' => array('name' => $guildName, 'id' => $guildId),
		'guilds' => $guildMenu,
		'items' => $guildItems,
		'sort' => 'type',
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/inventory.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("inventory.app.html.twig", $data);
	}
})->name('ryzomApp-Inventory/guild');

/*
 * Demande d'un tri spécifique pour l'inventaire
 */
$app->post('/ryzom/app/inventory/:guildId(/)', 'checkRequest', 'isGuilded', function($guildId) use ($app, $guildResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guild = $guildResource->get($guildId);
	if($guild==null) {
		$app->redirect('/ryzom/app/inventory?checksum='.$checksum.'&user='.$user);
	}
	$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
	if($userData['guild_id']!=$guildId) {
		if($userData['grade']!="Leader" && $userData['grade']!="HighOfficer") {
			$app->redirect('/ryzom/app/inventory?checksum='.$checksum.'&user='.$user);
		}
		$isRelated = false;
		foreach($guilds as $g) {
			if($g['id']==$guildId) {
				$isRelated = true;
				break;
			}
		}
		if(!$isRelated) {
			$app->redirect('/ryzom/app/inventory?checksum='.$checksum.'&user='.$user);
		}
	}
	$guildItems = getGuildItems($guild['apiKey']);
	$sort = "type";
	if(!isset($guildItems['error'])) {
		$sortType = $app->request()->params('type');
		$sortQuality = $app->request()->params('quality');
		if(($sortType==null && $sortQuality==null) || ($sortType!=null && $sortQuality!=null)) {
			usort($guildItems, 'sortByType');
		}
		else {
			if($sortType!=null) {
				usort($guildItems, 'sortByType');
			}
			if($sortQuality!=null) {
				usort($guildItems, 'sortByQuality');
				$sort = "quality";
			}
		}
	}
	$guildName = $guild['name'];
	$guildMenu = array();
	array_push($guildMenu, array('id' => $userData['guild_id'], 'name' => $userData['guild_name']));
	if($userData['grade']=="HighOfficer" || $userData['grade']=="Leader") {
		$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($userData['guild_id']);
		foreach($guilds as $guild) {
			array_push($guildMenu, $guild);
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'guild' => array('name' => $guildName, 'id' => $guildId),
		'guilds' => $guildMenu,
		'items' => $guildItems,
		'sort' => $sort,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/inventory.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("inventory.app.html.twig", $data);
	}
})->name('ryzomApp-Inventory/guild.post');

/*
 * Page d'accueil pour voir les compétences des membres
 */
$app->get('/ryzom/app/skills(/)', 'checkRequest', 'isGuilded', function() use ($app) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	// @TODO
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
$app->get('/ryzom/app/skills/harvest(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getHarvestLevels($homin['apiKey'], 7);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
$app->get('/ryzom/app/skills/craft(/)', 'checkRequest', 'isGuilded', function() use ($app) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	// @TODO
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
 * Affichage des compétences d'artisanat en boucliers
 */
$app->get('/ryzom/app/skills/craft/shield(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 3);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.shield.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.shield.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/Shield');

/*
 * Affichage des compétences d'artisanat en armures légères
 */
$app->get('/ryzom/app/skills/craft/armor/light(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 0);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.armor.light.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.armor.light.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/LightArmor');

/*
 * Affichage des compétences d'artisanat en armures moyennes
 */
$app->get('/ryzom/app/skills/craft/armor/medium(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 1);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.armor.medium.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.armor.medium.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/MediumArmor');

/*
 * Affichage des compétences d'artisanat en armures lourdes
 */
$app->get('/ryzom/app/skills/craft/armor/heavy(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 2);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.armor.heavy.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.armor.heavy.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/HeavyArmor');

/*
 * Affichage des compétences d'artisanat en bijoux
 */
$app->get('/ryzom/app/skills/craft/jewel(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 4);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.jewel.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.jewel.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/Jewel');

/*
 * Affichage des compétences d'artisanat en armes à une main
 */
$app->get('/ryzom/app/skills/craft/weapon/melee1(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 5);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.weapon.melee1.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.weapon.melee1.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/Melee1Weapon');

/*
 * Affichage des compétences d'artisanat en armes à deux mains
 */
$app->get('/ryzom/app/skills/craft/weapon/melee2(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 6);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.weapon.melee2.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.weapon.melee2.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/Melee2Weapon');

/*
 * Affichage des compétences d'artisanat en armes à distance
 */
$app->get('/ryzom/app/skills/craft/weapon/range(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getCraftLevels($homin['apiKey'], 7);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl, 10);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/craft.weapon.range.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("craft.weapon.range.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Craft/RangeWeapon');

/*
 * Affichage des compétences de magie
 */
$app->get('/ryzom/app/skills/magic(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getMagicLevels($homin['apiKey'], 7);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
 * Affichage du menu compétences de combat
 */
$app->get('/ryzom/app/skills/fight(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	// @TODO
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
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
 * Affichage des compétences de combat à mains nues
 */
$app->get('/ryzom/app/skills/fight/melee0(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getFightLevels($homin['apiKey'], 0);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/fight.melee0.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("fight.melee0.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Fight/Melee0');

/*
 * Affichage des compétences de combat à une main
 */
$app->get('/ryzom/app/skills/fight/melee1(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getFightLevels($homin['apiKey'], 1);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/fight.melee1.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("fight.melee1.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Fight/Melee1');

/*
 * Affichage des compétences de combat à deux mains
 */
$app->get('/ryzom/app/skills/fight/melee2(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getFightLevels($homin['apiKey'], 2);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/fight.melee2.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("fight.melee2.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Fight/Melee2');

/*
 * Affichage des compétences de combat à distance
 */
$app->get('/ryzom/app/skills/fight/range(/)', 'checkRequest', 'isGuilded', function() use ($app, $hominResource) {
	$user = $app->request()->params('user');
	$checksum = $app->request()->params('checksum');
	$userData = unserialize(base64_decode($user));
	$guildMembers = $hominResource->getEntityManager()->getRepository('\App\Entity\Homin')->getGuildMemberKeys($userData['guild_id']);
	$homins = array();
	foreach($guildMembers as $homin) {
		$lvl = getFightLevels($homin['apiKey'], 3);
		if(isset($lvl['error'])) {
			array_push($homins, array('name' => $homin['name'], 'error' => true));
		}
		else {
			$lvl = $homin['name']==$userData['char_name']?$lvl:roundLevels($lvl);
			array_push($homins, array('name' => $homin['name'], 'lvls' => $lvl));
		}
	}
	$data = array(
		'user' => $user,
		'checksum' => $checksum,
		'homins' => $homins,
		'grade' => $userData['grade'],
		'name' =>$userData['char_name']
	);
	$ig = $app->request()->params('ig');
	if($ig!=null) {
		echo $app->view->render("ingame/fight.range.ig.html.twig", $data);
	}
	else {
		echo $app->view->render("fight.range.app.html.twig", $data);
	}
})->name('ryzomApp-Skills/Fight/Range');

/*
 * Route d'affichage pour les erreurs critiques
 */
$app->get('/ryzom/app/error/:message(/)', function($message) use ($app) {
	$data = array('errorText' => urldecode($message));
	echo $app->view->render("error.html.twig", $data);
})->name('ryzomApp-Error');

?>
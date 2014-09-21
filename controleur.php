<?php
/*
 * controleur.php
 * 
 * Copyright 2014 wushepeng@gmx.fr
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

/* Controleur */
/* Situations possibles :
 * 	appel depuis l'extérieur des wabapps : message d'erreur sybilin
 * 	appel depuis un compte ryzom :
 * 		appel depuis un compte membre : compétences des membres de sa propre guilde, possibilité de saisir ou modifier sa propre clé api
 * 		appel depuis un compte officier : compte membre + inventaire de sa propre guilde
 *		appel depuis un compte offsup ou chef : compte membre + inventaire de tous les GH configurés + insertion ou modification de clé de guilde
 *  	appel depuis le formulaire config : enregistrement de la config avant de faire le reste
*/

// Début de la récupération des infos : GET ou POST ?


if (empty($_GET['checksum'])){
	if (empty($_POST['checksum'])){
		// ni GET ni POST
		require_once( 'vide.php');
		return 1;
	} else {
		// POST
		$user=$_POST['user'];
		$checksum=$_POST['checksum'];
		// IG, on trouve deux éléments dans le POST : nomdubouton_x et nomdubouton_y
		if (!empty($_POST['inventairemat']) || ($_POST['inventairemat_x']=="0")){
			$inventaire=True;
			$tri="triparmat";
		} else {
			if (!empty($_POST['inventaireQ']) || ($_POST['inventaireQ_x']=="0")){
				$inventaire=True;
				$tri="triparQ";
			} else {
				$inventaire=False;
				// Tester si mode config
				if (!empty($_POST["enregistrerconfig"]) || ($_POST['enregistrerconfig_x']=="0" )){
					$enregistrerconfig=True;
				} else {
					$enregistrerconfig=False;
				}
				if (!empty($_POST['savekeys']) || ($_POST['savekeys_x']=="0")){
					$enregistrer=True;
				} else {
					$enregistrer=False;
				}
				//echo "Dans inventaire faux";
				//var_dump($_POST);
			}
		}
		if (!empty($_POST['ig'])){
			$ingame=True;
		} else {
			$ingame=False;
		}
	}
} else {
	$user=$_GET['user'];
	$checksum=$_GET['checksum'];
	if ($_GET['ig']==1){
		$ingame=True;
	} else {
		$ingame=False;
	}
}

session_start();
require_once( "ryapi.php" );
require_once( "ryzom_extra.php" );
// enregistrer une variable pour protéger la config
require_once('config.php'); // config de la BDD
require_once('config.inc.php'); // récup des infos d'utilisateur et de la config (RYAPI_APP_KEY, etc)
ryapi_init();
// vérification de la checksum
$hashmac=hash_hmac('sha1', $user, RYAPI_APP_KEY);
if ($hashmac!==$checksum){
	require_once('erreurhmac.php');
	return 2;
}
// décodage de l'utilisateur
$data = unserialize(base64_decode($user));

// Ici, on peut facilement faire un log
// Avec timestamp, id, char_name, guild_id, guild_name (et aussi ig ou non)
$logstring=date("c")." - ".$data['id']." - ".$data['char_name']." - ".$data['guild_id']." - ".$data['guild_name'];
if ($ingame){
	$logstring.=" - ingame";
}
if ($inventaire){
	$logstring.=" - Inventaire\n";
} else {
	$logstring.="\n";
}

// une fonction de modele avec acces bdd pour le log
include('log.inc.php');


// membre d'une guilde, récupérer son id de guilde
$guildid=$data['guild_id'];
// écupérer les infos de guilde principale
$guildapikeys=array();
require_once('mainguild.inc.php'); // définit $mainguild et $guildapikeys 
// A FAIRE : la création de guilde n'est pas faite
$apikeys=array();
require_once('chars.inc.php'); // definit $apikeys
require_once('header.inc.php');
$grade=$data['grade'];
if ($enregistrerconfig){
	include_once('enregistrer_config.inc.php');
}
if ($enregistrer){
	include_once('enregistrer_mainconfig.inc.php');
}
if ($ingame && !$inventaire){
	// Afficher un avertissement, un bout de vue dans le controleur
	// A FAIRE basculer en vue
	echo "<h3>Attention, l'affichage en jeu est un peu dégradé</h3>\n";
}
if ($grade=="Member"){
	// directement les apprentissages
	include_once('apprentissage.inc.php');
} else {
	// Officier ou Off sup ou chef
	// A modifier
	if ($grade == "Officer"){
		// menu
		if ($inventaire){
			include_once('menuoff.inc.php');
			require_once('inventaire.inc.php'); // défini une fonction d'affichage
			
			//inventaire('La Firme',$guildapikeys['La Firme'],$tri);
			inventaire($mainguild,$guildapikeys[$mainguild],$tri);
		} else {
			include_once('menuoff.inc.php');
			include_once('apprentissage.inc.php');
		}
	} else {
		if ($inventaire){				
			include_once('menusup.inc.php');
			require_once('inventaire.inc.php'); // défini une fonction d'affichage
			foreach($guildapikeys as $guilde => $apikey){
				inventaire($guilde,$apikey,$tri);
			}
		} else {
			include_once('menusup.inc.php');
			include_once('apprentissage.inc.php');
			if ($grade=="Leader"){
				include_once('mainconfigform.inc.php');
			}
		}
	}
}
// trace pour debug
if ($ingame){
	//var_dump($_POST);
	//var_dump($_GET);
}
require_once('footer.inc.php');

?>

<?php
/* Copyright 2014 wushepeng@gmx.fr
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.*/

function apprentissage($nomutilisateur,$apikey,$sigle,$floor){
	// $nomutilisateur : chaine de carcactère, nom du perso
	// $apikey : chaine de caractère, clé api à utiliser
	// $sigle est le sigle du premier cran de la branche
	// $floor : booléen, détermine si on arrondi les valeurs ou non
	# colonnes dutableau : nom, forage (foret,lac,desert,jungle,primes)
	require_once('fonctions_perso.php');
	require_once('ryzom_extra.php');
	$lignetableau = '<tr>';
	$lignetableau .= "<td>$nomutilisateur</td>\n";
	$result = ryzom_character_api($apikey);
	$xml=$result[$apikey];
	$skills=(Array)$xml->skills;
	$forages=array();
	foreach ($skills as $titre=>$valeur){
		// On s'assure que le skill soit de la bonne branche avant de travailler dessus
		if (substr_compare($titre,$sigle,0,2)==0){//ryzom_translate((string)$item->sheet,,'fr',0).'(Q'.(int)$item->quality.$details.')" /> ');
			// d'abord faire un tableau des forages pour éviter les doublons
			if ($floor){
				$forages[fofotrans($titre)]=floor((floor($valeur) / 25))*25;
			} else {
				$forages[fofotrans($titre)]=$valeur;
			}
		}
	}
	$lignetableau .= "<td class=\"c".$forages['Forage']."\">".$forages['Forage']."</td><td class=\"c".$forages['Forage en désert']."\">".$forages['Forage en désert']."</td><td class=\"c".$forages['Forage en foret']."\">".$forages['Forage en foret']."</td><td class=\"c".$forages['Forage en jungle']."\">".$forages['Forage en jungle']."</td><td class=\"c".$forages['Forage lacustre']."\">".$forages['Forage lacustre']."</td><td class=\"c".$forages['Forage en primes racines']."\">".$forages['Forage en primes racines']."</td>\n";
	
	$lignetableau .= '</tr>';
	return $lignetableau;
}
function artisanat($nomutilisateur,$apikey,$sigle,$pasmoi,$user,$checksum,$dbconn){
	// $nomutilisateur : chaine de carcactère, nom du perso
	// $apikey : chaine de caractère, clé api à utiliser
	// $sigle est le sigle du premier cran de la branche
	// $moimeme : booléen, détermine si on arrondi les valeurs ou non et si on met la config
	// $checksum : pour le formulaire de config
	# colonnes dutableau : nom, forage (foret,lac,desert,jungle,primes)
	require_once('fonctions_perso.php');
	require_once('ryzom_extra.php');
	if (!$pasmoi){
		$lignetableau="<tr><td></td><td><h3>Les cases à cocher servent à choisir les compétences que vous désirez montrer.</h3></td></tr>\n<tr>";
	} else {
		$lignetableau = '<tr>';
	}
	$lignetableau .= "<td>$nomutilisateur</td>\n";
	$result = ryzom_character_api($apikey);
	$xml=$result[$apikey];
	//var_dump($xml);
	$skills=(Array)$xml->skills;
//	if ($nomutilisateur == 'Lorzipacna') {
//		var_dump($skills);
//	}
	$lignetableau .= "<td>";
	//var_dump($fofotrans);
	$forages=array();
	if (! $pasmoi){
		$lignetableau.="<form name=\"config\" action=\"controleur.php\" method=\"POST\">	<input type=\"hidden\" value=\"".$user."\" id=\"user\" name=\"user\" />
	<input type=\"hidden\" value=\"".$checksum."\" id=\"checksum\" name=\"checksum\" />
\n";
	}
	foreach ($skills as $titre=>$valeur){
		// On s'assure que le skill soit de la bonne branche avant de travailler dessus
		if (substr_compare($titre,$sigle,0,2)==0){
			// d'abord faire un tableau pour éviter les doublons
			if ($pasmoi){
				$forages[generaltrad($titre)]=floor((floor($valeur) / 25))*25;
			} else {
				$forages[generaltrad($titre)]=$valeur;
			}
		}
	}
	$afficher=Array();
	include('lireconfig.inc.php');
	//if (file_exists($nomutilisateur.".cfg")){
		// défini une variable $afficher
		//include('lireconfig.inc.php');
		//include($nomutilisateur.".cfg");
	//} else {
	//	echo "<h2>Pas de config $nomutilisateur</h2>";
	//}
	foreach ($forages as $forage=>$valeur){
		if (! $pasmoi){
			if (empty($afficher) || ($afficher[str_replace(' ','_',$forage)] == 'on')){
				$lignetableau .=  $forage." : ".$valeur . "<input type=\"checkbox\" id=\"$forage\" name=\"$forage\" checked=\"checked\"/> - ";
			} else {
				//var_dump($afficher);
				//echo $forage;
				//echo $afficher[str_replace(' ','_',$forage)];
				//echo str_replace(' ','_',$forage);
				$lignetableau .=  $forage." : ".$valeur . "<input type=\"checkbox\" id=\"$forage\" name=\"$forage\" /> - ";
			}
		} else {
			if (empty($afficher) || ($afficher[str_replace(' ','_',$forage)] == 'on')){
				$lignetableau .=  $forage." : <span class=\"c".$valeur."\">".$valeur . "</span> - ";
			}
		}
	}
	if (! $pasmoi){
		$lignetableau.="<input type=\"submit\" name=\"enregistrerconfig\" value=\"Enregistrer la configuration\" /></form>\n";
	}
	
	$lignetableau .= "</td></tr><tr><td>&nbsp;</td><td></td></tr>\n";
	return $lignetableau;
}

echo "<h1>Compétences des membres de ".$data['guild_name'].", arrondies par tranches de 25</h1>\n<h2>Compétences de forages</h2>\n";
echo "<table><tr><td>Nom</td><td>Forage</td><td>Forage en désert</td><td>Forage en for&ecirc;t</td><td>Forage en jungle</td><td>Forage lacustre</td><td>Forage en primes racines</td></tr>\n";
foreach ($apikeys as $apinom => $apikey){
	if ($data['char_name']==$apinom){
		echo apprentissage($apinom,$apikey,'sh',false);
	} else {
		echo apprentissage($apinom,$apikey,'sh',true);
	}
}
echo "</table>\n";
echo "<h2>Compétences d'artisanat</h2>\n";
echo "<table><tr><td>Nom</td><td>Artisanat</td></tr>\n";

foreach ($apikeys as $apinom => $apikey){
	// faire une boucle sur la requete SQL de recherche de config
	$dbconn = new mysqli($dbhost,$dbuser,$dbpassword,$dbname);	
	if ($dbconn->connect_errno){
		require('noconfig.php');
	}
	if ($data['char_name']==$apinom){
		echo artisanat($apinom,$apikey,'sc',false,$user,$checksum,$dbconn);
	} else {
		echo artisanat($apinom,$apikey,'sc',true,"","",$dbconn);
	}
	$dbconn->close();
}
echo "</table>\n";

<?php
// il faut récupérer l'id de la guilde
if (!empty($_POST['clenouvelle'])){
	$cle=$_POST['clenouvelle'];
	$xml=ryzom_guild_api($cle);
	$idnouvelle=$xml[$cle]->gid;
	$SQL="INSERT INTO ".$dbprefixe."guilde_api VALUES(\"".$idnouvelle."\",\"".$_POST['nomnouvelle']."\",\"".$cle."\",\"".$guildid."\")";
	$dbconn = new mysqli($dbhost,$dbuser,$dbpassword,$dbname);	
	if ($dbconn->connect_errno){
		require('noconfig.php');
	} else {
		$result=$dbconn->query($SQL);
		if (!$result=$dbconn->query($SQL)){
			echo $dbconn->error;
		}
	}
}
if (!empty($_POST['clenouveau'])){
	$cle=$_POST['clenouveau'];
	$xml=ryzom_character_api($cle);
	//var_dump($xml);
	$idnouveau=$xml[$cle]->id;
	$SQL="INSERT INTO ".$dbprefixe."perso_api VALUES(\"".$idnouveau."\",\"".$_POST['nomnouveau']."\",\"".$cle."\",\"".$guildid."\")";
	$dbconn = new mysqli($dbhost,$dbuser,$dbpassword,$dbname);	
	if (!$result=$dbconn->query($SQL)){
		echo $dbconn->error;
	}
	//echo $SQL;
	$dbconn->close();
}

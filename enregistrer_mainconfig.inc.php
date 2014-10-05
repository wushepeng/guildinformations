<?php
// il faut récupérer l'id de la guilde
if (!empty($_POST['clenouvelle'])){
	$dbconn = new mysqli($dbhost,$dbuser,$dbpassword,$dbname);	
	$cle=$dbconn->real_escape_string($_POST['clenouvelle']);
	$nomguilde=$dbconn->real_escape_string($_POST['nomnouvelle']);
	$xml=ryzom_guild_api($cle);
	$idnouvelle=$xml[$cle]->gid;
	$SQL="INSERT INTO ".$dbprefixe."guilde_api VALUES('".$idnouvelle."','".$nomguilde."','".$cle."','".$guildid."')";
	//$SQL=mysqli_real_escape_string($dbconn,$SQL);
	//echo $SQL;
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
	$dbconn = new mysqli($dbhost,$dbuser,$dbpassword,$dbname);	
	$cle=$dbconn->real_escape_string($_POST['clenouveau']);
	$nommembre=$dbconn->real_escape_string($_POST['nomnouveau']);
	$xml=ryzom_character_api($cle);
	//var_dump($xml);
	$idnouveau=$xml[$cle]->id;
	$SQL="INSERT INTO ".$dbprefixe."perso_api VALUES(\"".$idnouveau."\",\"".$nommembre."\",\"".$cle."\",\"".$guildid."\")";
	//$SQL=mysqli_real_escape_string($dbconn,$SQL);
	if (!$result=$dbconn->query($SQL)){
		echo $dbconn->error;
	}
	//echo $SQL;
	$dbconn->close();
}

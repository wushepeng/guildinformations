<?php
/* Copyright 2014 wushepeng@gmx.fr
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.*/

	//$configfile=fopen($data['char_name'].".cfg","w");
	//fwrite($configfile,"<?php \$afficher=");
	//fwrite($configfile,var_export($_POST,True));
	//fwrite($configfile,";");
	//fclose($configfile);
	// faire une une boucle - $data['id'] est l'id utilisateur
	//var_dump($_POST);
	//echo $SQL;
	$dbconn = new mysqli($dbhost,$dbuser,$dbpassword,$dbname);	
	$cleapi=$dbconn->real_escape_string($_POST['apikey']);
	$SQL="BEGIN;\nDELETE FROM ".$dbprefixe."perso_api WHERE nomperso=\"".$data['char_name']."\";\n";
	$SQL.="INSERT INTO ".$dbprefixe."perso_api VALUES(\"".$data['id']."\",\"".$data['char_name']."\",\"".$cleapi."\",\"".$data['guild_id']."\");\n";
	$SQL.="COMMIT;\n";
	//$SQL=mysqli_real_escape_string($dbconn,$SQL);
	if ($dbconn->connect_errno){
		require('noconfig.php');
	} else {
		$result=$dbconn->multi_query($SQL);
		if (!$result=$dbconn->query($SQL)){
			echo $dbconn->error;
		}
	}

<?php
/* Copyright 2014 wushepeng@gmx.fr
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.*/
	include('config.php');
	$SQL="SELECT * FROM ".$dbprefixe."perso_config WHERE nomperso=\"".$nomutilisateur."\";";
	//echo $SQL;
	$result=$dbconn->query($SQL);
	if (!$result=$dbconn->query($SQL)){
		echo $dbconn->error;
	}
	while ($ligne=$result->fetch_assoc()){
		$comp=$ligne['codecomp'];
		$afficher[$comp]="on";
	}

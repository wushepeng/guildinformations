<?php
/* Copyright 2014 wushepeng@gmx.fr
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.*/
 
require_once('fonctions_perso.php');

function inventaire($nomguilde,$apikey,$inventaire){
	$xml=recup_infos($apikey);
	//var_dump($xml);
	if ($xml){
		$items = array();
		$taille=0;
		foreach ($xml->room->item as $item) 
		{
			$items[$taille++]=affichage_item($item);
		}
		
		//var_dump($items);
		$items=choixdutri($items,$inventaire);
		if ($inventaire=="triparQ"){
			$items=choixdutri($items,"triparQ");
		} else {
			$items=choixdutri($items,"");
		}
		//var_dump($items);
		echo "<h2>$nomguilde ($taille objets)</h2>\n";
		foreach ( $items as $item )
		{
			echo $item;
		}
		
	}
}

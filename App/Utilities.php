<?php

function getItems($guildId, $grade) {
	$guildResource = new \App\Resource\GuildResource();
	$mainGuild = $guildResource->get($guildId);
	$mainItems = getGuildItems($mainGuild['apiKey']);
	$stuff = array();
	array_push($stuff, array('guild' => $mainGuild, 'items' => $mainItems));
	if($grade=="Leader" || $grade=="HighOfficer") {
		$guilds = $guildResource->getEntityManager()->getRepository('\App\Entity\Guild')->getRelatedGuilds($guildId);
		foreach($guilds as $guild) {
			$guildItems = getGuildItems($guild['apiKey']);
			array_push($stuff, array('guild' => $guild, 'items' => $guildItems));
		}
	}
	return $stuff;
}

function getGuildItems($guildKey) {
	$xml = ryzom_guild_api($guildKey);
	$infos = $xml[$guildKey];
	$items = array();
	foreach($infos->room->item as $item) {
		$url = ryzom_item_icon_url((string) $item->sheet, (int) $item->craftparameters->color, (int) $item->quality, (int) $item->stack);
		$stack = (int) $item->stack;
		$name = ryzom_translate((string) $item->sheet, 'fr', 0);
		$quality = (int) $item->quality;
		$details = "";
		foreach((array) $item->craftparameters as $nom => $detail) {
			$details .= " - ".$nom." : ".$detail;
		}
		array_push($items, array('iconUrl' => $url, 'name' => $name, 'quality' => $quality, 'stack'=> $stack, 'details' => $details));
	}
	return $items;
}

function getHominLevels($apiKey, $branch) {
	$xml = ryzom_character_api($apiKey);
	$infos = $xml[$apiKey];
	$skills = (Array) $infos->skills;
	$lvl;
	if($branch=='h') { // forage
		$lvl = getHarvestLevels($skills, "sh");
	}
	else if($branch=='c') { // craft
		$lvl = getConfigurableLevels($skills, "sc");
	}
	else if($branch=='m') { // magie
		$lvl = getMagicLevels($skills, "sm");
	}
	else if($branch=='f') { // combat
		$lvl = getConfigurableLevels($skills, "sf");
	}
	return $lvl;
}

function getHarvestLevels($skills, $branchCode) {
	$lvls = array(
		'desert' => 0,
		'forest' => 0,
		'jungle' => 0,
		'lakes' => 0,
		'primes' =>0
	);
	foreach($skills as $name => $value) {
		if(substr_compare($name, $branchCode, 0, 2)==0) {
			if($name == "sh" || $name == "shf") {
				if($value > $lvls['desert'] && $value > $lvls['forest'] && $value > $lvls['jungle'] && $value > $lvls['lakes'] && $value > $lvls['primes']) {
					foreach($lvls as $lvlName => $lvlValue) {
						$lvls[$lvlName] = $value;
					}
				}
			}
			else {
				if(substr_compare($name, 'd', 3, 1)==0) {
					if($value > $lvls['desert']) {
						$lvls['desert'] = $value;
					}
				}
				else if(substr_compare($name, 'f', 3, 1)==0) {
					if($value > $lvls['forest']) {
						$lvls['forest'] = $value;
					}
				}
				else if(substr_compare($name, 'j', 3, 1)==0) {
					if($value > $lvls['jungle']) {
						$lvls['jungle'] = $value;
					}
				}
				else if(substr_compare($name, 'l', 3, 1)==0) {
					if($value > $lvls['lakes']) {
						$lvls['lakes'] = $value;
					}
				}
				else {
					if($value > $lvls['primes']) {
						$lvls['primes'] = $value;
					}
				}
			}
		}
	}
	return $lvls;
}

function getConfigurableLevels($skills, $branchCode) {
	$lvls = array();
	foreach($skills as $name => $value) {
		if(substr_compare($name, $branchCode, 0, 2)==0) {
			array_push($lvls, array('code' => $name, 'value' => $value));
		}
	}
	return $lvls;
}

function getMagicLevels($skills, $branchCode) {
	$lvls = array(
		'heal' => 0,
		'neutra' => 0,
		'debi' => 0,
		'off' => 0
	);
	foreach($skills as $name => $value) {
		if(substr_compare($name, $branchCode, 0, 2)==0) {
			if($name == "sm") {
				if($value > $lvls['heal'] && $value > $lvls['neutra'] && $value > $lvls['debi'] && $value > $lvls['off']) {
					foreach($lvls as $lvlName => $lvlValue) {
						$lvls[$lvlName] = $value;
					}
				}
			}
			else if($name == "smd") {
				if($value > $lvls['heal'] && $value > $lvls['neutra']) {
					$lvls['heal'] = $value;
					$lvls['neutra'] = $value;
				}
			}
			else if($name == "smo") {
				if($value > $lvls['debi'] && $value > $lvls['off']) {
					$lvls['debi'] = $value;
					$lvls['off'] = $value;
				}
			}
			else {
				if(substr_compare($name, 'dh', 2, 2)==0) {
					if($value > $lvls['heal']) {
						$lvls['heal'] = $value;
					}
				}
				else if(substr_compare($name, 'da', 2, 2)==0) {
					if($value > $lvls['neutra']) {
						$lvls['neutra'] = $value;
					}
				}
				else if(substr_compare($name, 'oa', 2, 2)==0) {
					if($value > $lvls['debi']) {
						$lvls['debi'] = $value;
					}
				}
				else {
					if($value > $lvls['off']) {
						$lvls['off'] = $value;
					}
				}
			}
		}
	}
	return $lvls;
}

?>
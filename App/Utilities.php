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

?>
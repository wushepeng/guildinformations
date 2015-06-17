<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
	<title>Informations pour les membres de <?php echo $data['guild_name']; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.22" />
	<link rel="stylesheet" type="text/css" href="baluchon_main.css">
</head>
<body>
	<?php
	if ($ingame){
		echo '<style type="text/css">
td{border-bottom-width: 1px;border-bottom-style: solid;border-bottom-color: white;border-right-width: 1px;border-right-style: solid;border-right-color: white;}
.c0{color: red;}
.c25{color: #ff2200;}
.c50{color: #cc2200;}
.c75{color: #cc4400;}
.c100{color: #aa4400;}
.c125{color: #aa6600;}
.c150{color: #886600;}
.c175{color: #668800;}
.c200{color: #44aa00;}
.c225{color: #22cc00;}
.c250{color: green;}
</style>';
} 	?>	
	<div id="corps">
	<div id="bordH" class="ryzom-ui-t"><?php echo $data["char_name"]; ?><span style="float:right;margin-right:12px;"><a href="?checksum=<?php echo $checksum ?>&user=<?php echo $user ?>&mode=aide" class="ryzom-ui-text-button">Aide</a> | <a href="http://app.ryzom.com//index.php" class="ryzom-ui-text-button">Accueil</a></span></div>
	<div id="centre">

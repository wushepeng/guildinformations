<form name="mainconf" id="mainconf" action="controleur.php" method="POST">
	<input type="hidden" value="<?php echo $user; ?>" id="user" name="user" />
	<input type="hidden" value="<?php echo $checksum; ?>" id="checksum" name="checksum" />
	<h2>Clés API de Halls de guildes</h2>
<?php
	foreach ($guildapikeys as $name=>$key){
		echo"$name : $key<br/>\n";
	}
?>	
	Ajouter une clé : <input type="text" name="nomnouvelle" id="nomnouvelle" /> <input type="text" size="41" name="clenouvelle" id="clenouvelle"/><br/>
	<h2>Clés API de membres</h2>
<?php
	foreach ($apikeys as $name=>$key){
		echo"$name : $key<br/>\n";
	}
?>	
	Ajouter une clé : <input type="text" name="nomnouveau" id="nomnouveau" /> <input type="text" size="41" name="clenouveau" id="clenouveau"/><br/>
	<input type="submit" value="Enregistrer" name="savekeys" id="savekeys" />
</form>

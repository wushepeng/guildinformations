<form name="off" id="off" action="controleur.php" method="POST">
	<input type="hidden" value="<?php echo $user; ?>" id="user" name="user" />
	<input type="hidden" value="<?php echo $checksum; ?>" id="checksum" name="checksum" />
	Votre clé API personnelle : <br/>
	<input type="text" value="<?php $name=$data['char_name'];echo $apikeys["$name"]."\" name=\"apikey\" id=\"apikey\" ";
	if ($ingame){
		echo "size=\"300\"/>";
	} else {
		echo "size=\"41\"/>";
	}?>	<input type="submit" value="Enregistrer" name="savekey" id="savekey" />
</form>

<form name="off" id="off" action="controleur.php" method="POST">
	<input type="hidden" value="<?php echo $user; ?>" id="user" name="user" />
	<input type="hidden" value="<?php echo $checksum; ?>" id="checksum" name="checksum" />
	Inventaires : <input type="submit" value="Tri par matière ou type d'objet" name="inventairemat" id="inventairemat" />
	<input type="submit" value="Tri par qualité" name="inventaireQ" id="inventaireQ" /><br/>
	Votre clé API personnelle : <br/>
	<input type="text" value="<?php $name=$data['char_name'];echo $apikeys["$name"]; ?>" name="apikey" id="apikey" size="41"/>	<input type="submit" value="Enregistrer" name="savekey" id="savekey" />
</form>

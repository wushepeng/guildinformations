<form name="off" id="off" action="controleur.php" method="POST">
	<input type="hidden" value="<?php echo $user; ?>" id="user" name="user" />
	<input type="hidden" value="<?php echo $checksum; ?>" id="checksum" name="checksum" />
	Inventaire : <input type="submit" value="Tri par matière ou type d'objet" name="inventairemat" id="inventairemat" />
	<input type="submit" value="Tri par qualité" name="inventaireQ" id="inventaireQ" />
</form>

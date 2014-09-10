<?php
/* Copyright 2014 wushepeng@gmx.fr
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.*/

	$configfile=fopen($data['char_name'].".cfg","w");
	fwrite($configfile,"<?php \$afficher=");
	fwrite($configfile,var_export($_POST,True));
	fwrite($configfile,";");
	fclose($configfile);

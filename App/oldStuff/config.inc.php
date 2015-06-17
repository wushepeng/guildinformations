<?php
/*
 * config.inc.php
 * 
 * Copyright 2014 Wu She-Peng <wushepeng@gmx.fr>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

// extraire les données de la base

// Connexion
$dbconn = new mysqli($dbhost,$dbuser,$dbpassword,$dbname);	
if ($dbconn->connect_errno){
	require('noconfig.php');
	exit();
}
$SQL = "SELECT * FROM ".$dbprefixe."general_config LIMIT 1";
$result=$dbconn->query($SQL);
$config=$result->fetch_assoc();
define('RYAPI_APP_KEY',$config['RYAPI_APP_KEY']);
define('RYAPI_APP_URL',$config['RYAPI_APP_URL']);
define('RYAPI_APP_MAXAGE',$config['RYAPI_APP_MAXAGE']);

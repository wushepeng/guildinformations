<?php
//
// RyzomExtra - https://github.com/nimetu/ryzom_extra
// Copyright (c) 2012 Meelis Mägi <nimetu@gmail.com>
//
// This file is part of RyzomExtra.
//
// RyzomExtra is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// RyzomExtra is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program; if not, write to the Free Software Foundation,
// Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
//

/**
 * NOTE: this file uses a lot of memory. One language + item + resource stats is around 75MiB of memory
 *       might be best to export data sets to database if php memory is limited
 */
//error_reporting(E_ALL);

// patch where to find data sets
define('RYZOM_EXTRA_PATH', dirname(__FILE__).'/ryzom_extra/resources');
define('RYZOM_EXTRA_SHEETS_CACHE', RYZOM_EXTRA_PATH.'/sheets-cache');

/**
 * Get $sheetid translation from .suffix language file.
 * Include language file on first run and cache it.
 *
 * NOTE: sheetid is converted to lowercase.
 *       line breaks must be handled separately. they marked as "\n"
 *
 * @param string sheetid
 * @param string lang
 * @param mixed $index for titles 0=male and 1=female.
 *                     for anything else 'name', 'p', 'description', 'tooltip' (depends on sheetid type)
 *
 * @return string translated text, error message if language file or sheet id is not found
 */
function ryzom_translate($sheetid, $lang, $index=0){
	// memory usage for 1 language is around:
	// 4.7MiB creature, 70KiB faction, 8.5MiB item, 800KiB outpost, 600KiB place, 6MiB sbrick, 1MiB skill, 5MiB sphrase, 2MiB title, 4MiB uxt
	static $cache=array();

	// break up sheetid
	$_id = strtolower($sheetid);
	$_ext=strtolower(substr(strrchr($_id, '.'), 1));
	if($_ext===false || $_ext==''){
		$_ext='title'; // 'title' should be only one without 'dot' in sheetid
	}else{
		$_id=substr($_id, 0, strlen($_id)-strlen($_ext)-1);
	}

	// remap
	if($_ext=='sitem') $_ext='item';

	// 'Neutral' is not included in faction translation, so do it here
	if($_ext=='faction' && $_id=='neutral'){
		if($lang=='fr') {
			return 'Neutre';
		}else{
			return 'Neutral';
		}
	}

	// include translation file if needed
	if(!isset($cache[$_ext][$lang])){
		// use serialize/unserialize saves lot of memory
		$file = sprintf('%s/words_%s_%s.serial', RYZOM_EXTRA_SHEETS_CACHE, $lang, $_ext);
		$cache[$_ext][$lang]=ryzom_extra_load_dataset($file);
	}

	// remap id if full sheetid user requested is found
	if(isset($cache[$_ext][$lang][$sheetid])){
		$_id = $sheetid;
	}

	// check if translation is there
	if(!isset($cache[$_ext][$lang][$_id])){
		return 'NotFound:('.$_ext.')'.$lang.'.'.$sheetid;
	}

	// return translation - each may have different array 'key' for translation
	$word=$cache[$_ext][$lang][$_id];
	switch($_ext){
		case 'creature': // keys name and p
			// fall thru
		case 'faction' : // keys name, member
			// fall thru
		case 'item'    : // keys name, p, description
			// fall thru
		case 'outpost' : // keys name, description
			// fall thru
		case 'outpost_squad':
			// fall thru
		case 'outpost_building':
			// fall thru
		case 'place'   : // keys name
			// fall thru
		case 'sbrick'  : // keys name, p, description, tooltip
			// fall thru
		case 'skill'   : // keys name, p, description
			// fall thru
		case 'sphrase' : // keys name, p, description
			if(isset($word[$index])) return $word[$index];
			// fall back to 'name' index
			return $word['name'];
		case 'title'   : // keys name, women_name
			if((int) $index==0){
				return $word['name'];
			}else{
				return $word['women_name'];
			}
		// ui???? translations
		case 'uxt': //
			return $word['name'];
	}
	// should never reach here, but incase it does...
	return 'Unknown:'.$_ext.'.'.$_id;
}

/**
 * Converts binary sheet_id to string format
 *
 * @param  int   numeric sheet_id
 * @return mixed sheetid in string format or boolean FALSE if lookup failed
 */
function ryzom_sheetid_bin($sid_bin){
	// full list is around 120MiB
	static $cache = array();

	$idx = floor(intval($sid_bin) / 1000000);
	if(!isset($cache[$idx])){
		$cache[$idx] = ryzom_extra_load_dataset(sprintf('%s/sheets-%02x.serial', RYZOM_EXTRA_SHEETS_CACHE, $idx));
	}
	if(isset($cache[$idx][$sid_bin])){
		return $cache[$idx][$sid_bin]['name'].'.'.$cache[$idx][$sid_bin]['suffix'];
	}
	return false;
}

/**
 * Return building info based building id from API XML file
 * If building_id is unknown, then return empty array
 *
 * @param int $building_id
 * @return array
 */
function ryzom_building_info($building_id){
	static $cache=array();
	if(empty($cache)){
		$file= sprintf('%s/buildings.inc.php', RYZOM_EXTRA_PATH);
		if(!file_exists($file)){
			throw new Exception('Date file ['.$file.'] not found');
		}
		$cache=include($file);
	}
	if(!isset($cache[$building_id])){
		$result=array();
	}else{
		$result=$cache[$building_id];
	}
	return $result;
}

/**
 * Returns sheetid details
 *
 * @param $sheetid - with or without '.sitem'
 * @param $extra   - for items, also include craft plan to '_craftplan' index
 *                   for resources, include stats to '_stats' index
 * @return array
 */
function ryzom_item_info($sheetid, $extra=false){
	static $cache=array(); // ~ 20MiB, items

	// include data file if needed
	if(empty($cache)){
		// use serialize/unserialize saves lot of memory
		$file = sprintf('%s/items.serial', RYZOM_EXTRA_SHEETS_CACHE);
		$cache=ryzom_extra_load_dataset($file);
	}

	$_id=strtolower($sheetid);
	if(preg_match('/^(.*)\.sitem$/', $_id, $m)){
		$_id=$m[1];
	}

	if(!isset($cache[$_id])){
		$result=false;
		return $result;
	}
	$result=$cache[$_id];

	// fix some id's
	if(isset($result['craftplan'])) $result['craftplan'].='.sbrick';
	if(isset($result['skill'])) $result['skill'].='.skill';
	$result['sheetid'].='.sitem';

	// if item type is Resource, then also include stats
	if($extra==true){
		if($result['type']==RyzomExtra::TYPE_RESOURCE){
			$result['_stats']=ryzom_resource_stats($_id);
		}else if(isset($result['craftplan'])){
			$result['_craftplan']=ryzom_craftplan($result['craftplan']);
		}
	}

	return $result;
}

/**
 * Return resource craft stats like durability/lightness, etc
 *
 * @param $sheetid - with or without '.sitem'
 * @return mixed - FALSE if $sheetid not found
 */
function ryzom_resource_stats($sheetid){
	static $cache;// ~20MiB, resource stats cache

	if(empty($cache)){
		$file=sprintf('%s/resource_stats.serial', RYZOM_EXTRA_SHEETS_CACHE);
		$cache=ryzom_extra_load_dataset($file);
	}

	$_id=strtolower($sheetid);
	if(preg_match('/^(.*)\.sitem$/', $_id, $m)){
		$_id=$m[1];
	}

	if(isset($cache[$_id])){
		$result=$cache[$_id]['stats'];
	}else{
		$result=false;
	}
	return $result;
}

/**
 * Return sbrick details
 *
 * @param $sheetid with or without '.sbrick'
 *
 * @return mixed FALSE if $sheetid not found
 */
function ryzom_sbrick_info($sheetid) {
	static $cache;

	if (empty($cache)) {
		$file = sprintf('%s/sbrick.serial', RYZOM_EXTRA_SHEETS_CACHE);
		$cache = ryzom_extra_load_dataset($file);
	}
	$_id = strtolower($sheetid);
	if (preg_match('/^(.*)\.sbrick$/', $_id, $m)) {
		$_id = $m[1];
	}
	if (!isset($cache[$_id])) {
		$result = false;
		return $result;
	}
	return $cache[$_id];
}

/**
 * Return craft plan
 *
 * @param $sheetid - with or without '.sbrick'
 * @return unknown_type
 */
function ryzom_craftplan($sheetid){
	static $cache=array();
	if(empty($cache)){
		$file=sprintf('%s/craftplan.serial', RYZOM_EXTRA_SHEETS_CACHE);
		$cache=ryzom_extra_load_dataset($file);
	}

	$_id=strtolower($sheetid);
	if(preg_match('/^(.*)\.sbrick$/', $_id, $m)){
		$_id=$m[1];
	}

	if(isset($cache[$_id])){
		$result=$cache[$_id];
	}else{
		$result=false;
	}
	return $result;
}

/**
 * Return unformatted skilltree list
 *
 * @return unknown_type
 */
function ryzom_skilltree(){
	static $cache=array();
	if(empty($cache)){
		$file=sprintf('%s/skilltree.serial', RYZOM_EXTRA_SHEETS_CACHE);
		$cache=ryzom_extra_load_dataset($file);
	}

	return $cache;
}

/**
 * Visual slot index to sheet translation
 *
 * $slot is const from RyzomSheets EVisualSlot class
 *
 * @param int $slot
 * @param int $index
 *
 * @return string|bool
 */
function ryzom_vs_sheet($slot, $index){
	$cache = ryzom_extra_load_vs();

	if (isset($cache[$slot][$index])){
		return $cache[$slot][$index];
	}

	return false;
}

/**
 * Find visual slot index for requested sheet name
 *
 * @param int $slot
 * @param string $sheet
 *
 * @return bool|mixed
 */
function ryzom_vs_index($slot, $sheet){
	$cache = ryzom_extra_load_vs();

	if(!isset($cache[$slot])){
		return false;
	}
	return array_search($sheet, $cache[$slot], true);
}

/**
 * Load visual_slot.serial file
 *
 * @return array
 */
function ryzom_extra_load_vs(){
	static $cache = array();
	if (empty($cache)) {
		$file = sprintf('%s/visual_slot.serial', RYZOM_EXTRA_SHEETS_CACHE);
		$cache = ryzom_extra_load_dataset($file);
	}
	return $cache;
}

/**
 * Loads dataset and returns result.
 * Does not unmask unserialize/file_get_content warning/notice's
 *
 * throw Exception if file not found
 *
 * @param $file file name with full path
 * @return mixed
 */
function ryzom_extra_load_dataset($file){
	if(file_exists($file)){
		$result=unserialize(file_get_contents($file));
	}else{
		throw new Exception('Data file ['.$file.'] not found');
	}
	return $result;
}

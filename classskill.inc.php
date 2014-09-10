<?php
/*
 * classskill.inc.php
 * 
 * Copyright 2014 wushepeng@gmx.fr
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

class Skill{
	private skill_number;
	private skill_id;
	private max_level;
	private parent_node;
	private first_child=NULL;
	private first_brother=NULL;
	public function skill($skill_number,$skill_id,$max_level,$parent_node){
		$this->skill_number=$skill_number;
		$this->skill_id=$skill_id;
		$this->max_level=$max_level;
		$this->parent_node=$parent_node;
		// Il faut positionner le first_child du parent_node ou le first_brother du dernier frère créé
	}
	public function get_skill_number(){
		return $this->skill_number;
	}
	public function get_skill_id(){
		return $this->skill_id;
	}
	public function get_max_level(){
		return $this->max_level;
	}
	public function get_parent_node(){
		return $this->parent_node;
	}
	public function get_first_child(){
		return $this->first_child;
	}
	public function set_first_child(id){
		$this->first_child=id;
	}
	public function get_first_brother(){
		return $this->first_brother;
	}
	public function set_first_brother(id){
		$this->first_brother=id;
	}
}

?>

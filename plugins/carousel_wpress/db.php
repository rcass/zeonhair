<?php

class Carousel_wpress_db {
	
	var $wpdb;
	var $carousel_table_name;
	var $carousel_entries_table_name;
	
	function Carousel_wpress_db() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->carousel_table_name = $wpdb->prefix.'carousel_wpress';
		$this->carousel_entries_table_name = $wpdb->prefix.'carousel_entries_wpress';
	}
	
	function setup_tables() {
		self::create_carousel_tables();
		self::update_carousel_table();
	}
	
	//create tables
	function create_carousel_tables() {
		
		//carousel table
		$sql = "CREATE TABLE IF NOT EXISTS ".$this->carousel_table_name." (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`type_id` INT NULL,
		`name` VARCHAR( 120 ) NULL,
		`description_display` TINYINT NULL,
		`options` TEXT NULL
		);
		";
		$this->wpdb->query($sql);
		
		//carousel entries tables
		$sql = "CREATE TABLE IF NOT EXISTS ".$this->carousel_entries_table_name." (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`carousel_id` INT NULL ,
		`type_id` TINYINT NULL ,
		`title` VARCHAR( 120 ) NULL ,
		`url` VARCHAR( 255 ) NULL ,
		`image` VARCHAR( 255 ) NULL ,
		`description` TEXT NULL,
		`position` INT NULL
		);
		";
		$this->wpdb->query($sql);
	}
	
	function update_carousel_table() {
		$sql = "DESCRIBE $this->carousel_table_name";
		$result = $this->wpdb->get_results($sql, 'ARRAY_N');
		
		for($i=0; $i<count($result); $i++) {
			$field[] = $result[$i][0];
		}
		
		if(!in_array('type_id', $field)) {
			$sql = "ALTER TABLE `$this->carousel_table_name` ADD `type_id` INT NULL AFTER `id`";
			$this->wpdb->query($sql);
		}
		if(!in_array('description_display', $field)) {
			$sql = "ALTER TABLE `$this->carousel_table_name` ADD `description_display` TINYINT NULL AFTER `name`";
			$this->wpdb->query($sql);
		}
	}
	
	function get_carousel_list($criteria=array()) {
		$id = @$criteria['id'];
		
		$sql = "SELECT *
		FROM $this->carousel_table_name
		WHERE 1";
		
		if($id!='') $sql .= " AND id='$id'";
		
		$sql .= ' ORDER BY id DESC';
		
		//echo $sql;
		$result = $this->wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}
	
	function get_nb_entries_per_carousel() {
		
		$sql = "SELECT carousel_id id, count(*) nb
		FROM $this->carousel_entries_table_name
		WHERE 1 
		GROUP BY carousel_id";
		
		//echo $sql;
		$result = $this->wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}
	
	//Create Carousel
	function add_carousel($criteria=array()) {
		$this->wpdb->insert($this->carousel_table_name, $criteria);
		return $this->wpdb->insert_id;
	}
	
	//Edit Carousel
	function edit_carousel($criteria=array(), $where=array()) {
		$this->wpdb->update($this->carousel_table_name, $criteria, $where);
	}
	
	//Delete Carousel
	function delete_carousel($id) {
		if($id!='') {
			$sql = "DELETE FROM $this->carousel_table_name WHERE id='$id'";
			$this->wpdb->query($sql);
			$sql = "DELETE FROM $this->carousel_entries_table_name WHERE carousel_id='$id'";
			$this->wpdb->query($sql);
		}
	}
	
	function get_carousel_entries($criteria=array()) {
		$id = @$criteria['id'];
		$carousel_id = @$criteria['carousel_id'];
		
		$sql = "SELECT *
		FROM $this->carousel_entries_table_name
		WHERE 1";
		
		if($id!='') $sql .= " AND id='$id'";
		if($carousel_id!='') $sql .= " AND carousel_id='$carousel_id'";
		
		$sql .= ' ORDER BY position';
		
		$result = $this->wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}
	
	//Add Carousel Entry
	function add_carousel_entry($criteria=array()) {
		$this->wpdb->insert($this->carousel_entries_table_name, $criteria);
		return $this->wpdb->insert_id;
	}
	
	//Edit Carousel Entry
	function edit_carousel_entry($criteria=array(), $where=array()) {
		$this->wpdb->update($this->carousel_entries_table_name, $criteria, $where);
	}
	
	//Delete Carousel Entry
	function delete_carousel_entry($where=array()) {
		$this->wpdb->delete($this->carousel_entries_table_name, $where);
	}
	
	//Reorder Entries
	function reorder_entries($ids) {
		$idsTab = explode(',', $ids);
		if(count($idsTab)>0) {
			$position=1;
			foreach($idsTab as $id) {
				if($id!='') {
					$sql = "UPDATE $this->carousel_entries_table_name SET position='$position' WHERE id='$id'";
					$this->wpdb->query($sql);
					$position++;
				}
			}
		}
	}
}

new Carousel_wpress_db();

?>
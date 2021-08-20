<?php
class ModelCommonNewsletter extends Model {
	
	public function createNewsletter()
	{
			
		$res0 = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."newsletter'");
		if($res0->num_rows == 0){
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `".DB_PREFIX."newsletter` (
				    `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
                    `email` VARCHAR( 255 ) NOT NULL ,
                    `group` VARCHAR( 25 ) NOT NULL ,
                    `date_added` DATETIME NOT NULL ,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		}
		
		
	}
	
	public function getNewsLetter() {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."newsletter"); 

		return $query->rows;
	}

	public function deleteNewsletter($id) {
		$query = $this->db->query("DELETE FROM ". DB_PREFIX ."newsletter WHERE id=".$id); 
	}
}
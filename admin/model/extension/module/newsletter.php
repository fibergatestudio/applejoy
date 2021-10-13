<?php
class ModelExtensionModuleNewsletter extends Model {
	
	public function getTotalSubscribers() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter");
		
		return $query->row['total'];
	}	

	public function createDatabaseTable() {
		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."newsletter` ( ";
		$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`email` varchar(96) COLLATE utf8_bin DEFAULT NULL, ";
		$sql .= "PRIMARY KEY (`id`) ";
		$sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
		$this->db->query($sql);

	}
	
	
	public function dropDatabaseTable() {
		$sql = "DROP TABLE IF EXISTS `".DB_PREFIX."newsletter`;";
		$this->db->query($sql);
	}

}
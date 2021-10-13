<?php
class ModelExtensionModuleRepair extends Model {
	
	public function getTotalOrders() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "repair_order");
		
		return $query->row['total'];
	}	

	public function createDatabaseTable() {
		$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."repair_order` ( ";
		$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$sql .= "`name` varchar(96) NOT NULL, ";
		$sql .= "`phone` varchar(10) NOT NULL, ";
		$sql .= "`product` varchar(96) DEFAULT NULL, ";
		$sql .= "`message` text  DEFAULT NULL, ";
		$sql .= "`date` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
		$sql .= "PRIMARY KEY (`id`) ";
		$sql .= ") ENGINE=MyISAM";
		$this->db->query($sql);
	}
	
	
	public function dropDatabaseTable() {
		$sql = "DROP TABLE IF EXISTS `".DB_PREFIX."repair_order`;";
		$this->db->query($sql);
	}

	public function createRepairOrder()
	{			
		$res0 = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."repair_order'");
		if($res0->num_rows == 0){
			$sql  = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."repair_order` ( ";
			$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
			$sql .= "`name` varchar(96) NOT NULL, ";
			$sql .= "`phone` varchar(10) NOT NULL, ";
			$sql .= "`product` varchar(96) DEFAULT NULL, ";
			$sql .= "`message` text  DEFAULT NULL, ";
			$sql .= "`date` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
			$sql .= "PRIMARY KEY (`id`) ";
			$sql .= ") ENGINE=MyISAM";
			$this->db->query($sql);
		}	
	}
	
	public function getRepairOrder() {
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."repair_order"); 

		return $query->rows;
	}

	public function deleteRepairOrder($id) {
		$query = $this->db->query("DELETE FROM ". DB_PREFIX ."repair_order WHERE id=".$id); 
	}

}
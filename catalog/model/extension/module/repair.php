<?php
class ModelExtensionModuleRepair extends Model {
		
	public function addOrder($data) {
		   $this->db->query("INSERT INTO " . DB_PREFIX . "repair_order SET 
		   name='" .$data['name']. "',
		   phone='" .$data['phone']. "',
		   message='" .$data['message']. "',
		   product='" .$data['product']. "'
		   ");
	}

   
}
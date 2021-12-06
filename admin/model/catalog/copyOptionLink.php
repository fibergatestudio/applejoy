<?php
class ModelCatalogcopyOptionLink extends Model {

	public function addOption($data) {
		if (isset($data['product_option_link'])) {
			foreach ($data['product_option_link'] as $product_option) {
				if (isset($product_option['type'])) {
					if ($product_option['type'] == 'link') {
						if (isset($product_option['product_option_value'])) {
							if ($product_option['product_option_id']) {
								$product_option_id = $product_option['product_option_id'];
							} else {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_option['product_id'] . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");

								$product_option_id = $this->db->getLastId();
							}

							foreach ($product_option['product_option_value'] as $product_option_value) {

								if (isset($product_option_value['subtract'])){
									$product_option_value['subtract'] = $product_option_value['subtract'];
								} else{
									$product_option_value['subtract'] = 0;
								}

								if (isset($product_option_value['price_prefix'])){
									$product_option_value['price_prefix'] = $product_option_value['price_prefix'];
								} else{
									$product_option_value['price_prefix'] = 0;
								}

								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_option['product_id'] . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							}
						}
					}
				}
			}
		}
	}

	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND (pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%' OR p.model LIKE '" . $this->db->escape($data['filter_name']) . "%' OR p.sku LIKE '" . $this->db->escape($data['filter_name']) . "%')";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
}
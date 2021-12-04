<?php

class ControllerCatalogcopyOptionLink extends Controller {
	public function index() {

		$this->load->model('catalog/product');
		$this->load->model('catalog/option');

		$this->load->language('catalog/copyOptionLink');

		// Find which protocol to use to pass the full image link back
		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['module_ProductOptionLink_on_off_fullcheck'] = $this->config->get('module_ProductOptionLink_on_off_fullcheck');
		
		if ($this->config->get('module_ProductOptionLink_on_off_save')) {
			$data['module_ProductOptionLink_on_off_save'] = $this->config->get('module_ProductOptionLink_on_off_save');
		} else {
			$data['module_ProductOptionLink_on_off_save'] = 0;
		}

		$copydata = $this->request->post;
		
		if (isset($copydata['product_main'])) {
			$data['product_main'] = $copydata['product_main'];
		} else {
			$data['product_main'] = '';
		}

		if (isset($data['product_main']['option_value_id'])) {
			$option_info_main = $this->model_catalog_option->getOptionValue($data['product_main']['option_value_id']);
			$data['product_main']['option_value_id_name'] = $option_info_main['name'];
		} else {
			$data['product_main']['option_value_id_name'] = '';
		}

		if (isset($copydata['product_option_value'])) {
			$data['product_option_value'] = $copydata['product_option_value'];
		} else {
			$data['product_option_value'] = '';
		}

		if (isset($copydata['option_row'])) {
			$option_row = $copydata['option_row'];
		} else {
			$option_row = '';
		}

		if (isset($copydata['option_value_row'])) {
			$option_value_row = $copydata['option_value_row'];
		} else {
			$option_value_row = '';
		}

		if (isset($copydata['data_copyOptionLink'])) {
			$copyOptionLink_input = $copydata['data_copyOptionLink'];
		} else {
			$copyOptionLink_input = '';
		}

		if (isset($copydata['product_option'])) {
			$data['product_option'] = $copydata['product_option'];
		} else {
			$data['product_option'] = '';
		}

		if ($copyOptionLink_input) {
			foreach ($copyOptionLink_input as $copyOptionLink_input) {
				for ($i=0; $i<$option_value_row; $i++) {

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][option_value_id]') {

						$data['data_copyOptionLink'][$i]['option_value_id'] = $copyOptionLink_input['value'];

						$option_info = $this->model_catalog_option->getOptionValue($copyOptionLink_input['value']);
						if ($option_info)	{
							$data['data_copyOptionLink'][$i]['option_name']= $option_info['name'];
						}
					}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][quantity]') {

						$data['data_copyOptionLink'][$i]['quantity'] = $copyOptionLink_input['value'];

						$product_info = $this->model_catalog_product->getProduct($copyOptionLink_input['value']);
						if ($product_info)	{
							$data['data_copyOptionLink'][$i]['product_name']= $product_info['name'];
						}

						$data['data_copyOptionLink'][$i]['product_option'] = $this->model_catalog_product->getProductOptions($copyOptionLink_input['value']);
					}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][subtract]') {
							$data['data_copyOptionLink'][$i]['subtract'] = $copyOptionLink_input['value'];}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][price_prefix]'){
						$data['data_copyOptionLink'][$i]['price_prefix'] = $copyOptionLink_input['value'];}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][price]') {
						$data['data_copyOptionLink'][$i]['price'] = $copyOptionLink_input['value'];}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][points_prefix]') {
						$data['data_copyOptionLink'][$i]['points_prefix'] = $copyOptionLink_input['value'];}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][points]') {
						$data['data_copyOptionLink'][$i]['points'] = $copyOptionLink_input['value'];}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][weight_prefix]') {
						$data['data_copyOptionLink'][$i]['weight_prefix'] = $copyOptionLink_input['value'];}

					if ($copyOptionLink_input['name'] == 'product_option['.$option_row.'][product_option_value]['.$i.'][weight]') {
						$data['data_copyOptionLink'][$i]['weight'] = $copyOptionLink_input['value'];}
				}
			}
		}

		$this->response->setOutput($this->load->view('catalog/copyOptionLink', $data));
	}


	public function newOptionLink() {
		$this->load->model('catalog/copyOptionLink');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_catalog_copyOptionLink->addOption($this->request->post);
		}
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/copyOptionLink');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 8;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_copyOptionLink->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
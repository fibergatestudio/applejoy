<?php

class ControllerExtensionModuleFeatured extends Controller {

	public function index($setting) {

		$this->load->language('extension/module/featured');



		$this->load->model('catalog/product');



		$this->load->model('tool/image');

		$data["module_name"] = $setting["name"];



		$data['products'] = array();



		if (!$setting['limit']) {

			$setting['limit'] = 4;

		}



		if (!empty($setting['product'])) {

			$products = array_slice($setting['product'], 0, (int)$setting['limit']);



			$this->load->model('account/wishlist');



			foreach ($products as $product_id) {

				$product_info = $this->model_catalog_product->getProduct($product_id);

				$wish = $this->model_account_wishlist->productInWishlist($product_id);

				if ($product_info) {

					if ($product_info['image']) {

						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);

					} else {

						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);

					}



					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

					} else {

						$price = false;

					}



					if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {

						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

						$tax_price = (float)$product_info['special'];

					} else {

						$special = false;

						$tax_price = (float)$product_info['price'];

					}



					if ($this->config->get('config_tax')) {

						$tax = $this->currency->format($tax_price, $this->session->data['currency']);

					} else {

						$tax = false;

					}



					if ($this->config->get('config_review_status')) {

						$rating = $product_info['rating'];

					} else {

						$rating = false;

					}

					$categories = $this->model_catalog_product->getCategories($product_info['product_id']);

					$category_id = $categories[0]["category_id"];

					$parent_id = $this->model_catalog_product->get_parent_id((int)$category_id);

          while((int)$parent_id != 0){

						$parent_id = $this->model_catalog_product->get_parent_id((int)$category_id);

						if((int)$parent_id != 0){

							$category_id = $parent_id;

						}

					}



					$this->load->model('catalog/tree_cats');

					$category_data = $this->model_catalog_tree_cats->getCategory($category_id);





					$data['products'][] = array(

						'product_id'  => $product_info['product_id'],

						'thumb'       => $image,

						'name'        => $product_info['name'],

						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',

						'price'       => $price,

						'special'     => $special,

						'tax'         => $tax,

						'rating'      => $rating,

						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),

						'id_category' => $category_data["category_id"],

						'name_category' => $category_data["name"],

						'wish'        => $wish,

					);

				}

			}

		}

		$data['product_price'] = $this->language->get('product_price');
		$data['product_buy'] = $this->language->get('product_buy');
		$data['product_buy_one_click'] = $this->language->get('product_buy_one_click');



		if ($data['products']) {

			return $this->load->view('extension/module/featured', $data);

		}

	}



}


<?php
class ControllerAccountAccount extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/account');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$data['errors'] = $this->validateForm();
			if(empty($data['errors'])){
				if($this->request->post['current_password'] != '' && $this->request->post['new_password'] && $this->request->post['confirm']){
					$this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['new_password']);
				}
				$this->load->model('account/customer');
				$this->model_account_customer->save_update_customer($this->request->post, $this->customer->getId());
				if($this->customer->getAddressId() == "0"){
					if(trim($this->request->post['city']) != '' && trim($this->request->post['address_1']) != '' && trim($this->request->post['postcode']) != ''){
						$this->model_account_customer->add_address($this->request->post, $this->customer->getId());
					}else {
						$data['errors']['city'] = $this->language->get('full_address');
					}
				} else {
					$this->model_account_customer->update_address($this->request->post, $this->customer->getAddressId());
				}
				if(empty($data['errors'])){
					$data['errors']['success_save'] = $this->language->get('success_save');
				}
			}
		}
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);

		$data['credit_cards'] = array();

		$files = glob(DIR_APPLICATION . 'controller/extension/credit_card/*.php');

		foreach ($files as $file) {
			$code = basename($file, '.php');

			if ($this->config->get('payment_' . $code . '_status') && $this->config->get('payment_' . $code . '_card')) {
				$this->load->language('extension/credit_card/' . $code, 'extension');

				$data['credit_cards'][] = array(
					'name' => $this->language->get('extension')->get('heading_title'),
					'href' => $this->url->link('extension/credit_card/' . $code, '', true)
				);
			}
		}

		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);

		if ($this->config->get('total_reward_status')) {
			$data['reward'] = $this->url->link('account/reward', '', true);
		} else {
			$data['reward'] = '';
		}

		$data['return'] = $this->url->link('account/return', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['recurring'] = $this->url->link('account/recurring', '', true);

		$this->load->model('account/customer');

		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());

		if (!$affiliate_info) {
			$data['affiliate'] = $this->url->link('account/affiliate/add', '', true);
		} else {
			$data['affiliate'] = $this->url->link('account/affiliate/edit', '', true);
		}

		if ($affiliate_info) {
			$data['tracking'] = $this->url->link('account/tracking', '', true);
		} else {
			$data['tracking'] = '';
		}
		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		$this->load->model('account/address');
		if($customer_info["address_id"] != "0"){
			$address = $this->model_account_address->getAddress($customer_info["address_id"]);
			$customer_info["address_1"] = $address["address_1"];
			$customer_info["city"] = $address["city"];
			$customer_info["postcode"] = $address["postcode"];

		}
		$data['customer'] = $customer_info;

		$this->load->language('account/order');
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['orders'] = array();

		$this->load->model('account/order');

		$order_total = $this->model_account_order->getTotalOrders();

		$results = $this->model_account_order->getOrders(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$count_products_in_order = 0;
			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);
			$products_start = $this->model_account_order->getOrderProductsByOrderId($result['order_id']);

			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$products = [];
			foreach($products_start as $product_from_order){

				$count_products_in_order += (int)$product_from_order['quantity'];
				$product_info = $this->model_catalog_product->getProduct($product_from_order['product_id']);

				if ($product_info['image']) {
					$product_info['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				} else {
					$product_info['thumb'] = '';
				}
				$product_info['quantity'] = $product_from_order['quantity'];
				$product_info['total'] = $product_from_order['total'];
				$products[] = $product_info;
			}

			$data['orders'][] = array(
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'email'      => $result['email'],
				'telephone'  => $result['telephone'],
				'postcode'   => $result['payment_postcode'],
				'city'       => $result['payment_city'],
				'status'     => $result['status'],
				'payment'    => $this->language->get($result['payment_method']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'products'   => $products,
				'total_products' => $count_products_in_order,//($product_total + $voucher_total),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'view'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),
				'repeat'       => $this->url->link('account/order/repeat', 'order_id=' . $result['order_id'], true),
			);
		}



		$this->load->language('account/wishlist');

		$this->load->model('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['wishlist'] = array();

		$results = $this->model_account_wishlist->getWishlist();

		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);

			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				} else {
					$image = false;
				}

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				$data['wishlist'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'stock'      => $stock,
					'price'      => $price,
					'special'    => $special,
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
				);
			} else {
				$this->model_account_wishlist->deleteWishlist($result['product_id']);
			}
		}

		$wish_total = $this->model_account_wishlist->getTotalWishlist();

		$url = $this->url->link('account/account', 'page={page}', true);
		$text_next = $this->language->get('text_next');
		$text_prev = $this->language->get('text_prev');
		$html_pagination = $this->render_pagination($order_total, $page, 10, $url, $text_next, $text_prev, 5);
		$data['pagination'] = $html_pagination;

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$data["text_my_account"] = $this->language->get('text_my_account');
		$data["user_data_text"] = $this->language->get('user_data_text');
		$data["my_orders_text"] = $this->language->get('my_orders_text');
		$data["wishlist_text"] = $this->language->get('wishlist_text');
		$data["edit_account_data"] = $this->language->get('edit_account_data');
		$data["your_name"] = $this->language->get('your_name');
		$data["your_lastname"] = $this->language->get('your_lastname');
		$data["your_telephone"] = $this->language->get('your_telephone');
		$data["your_email"] = $this->language->get('your_email');
		$data["address_shipping_text"] = $this->language->get('address_shipping_text');
		$data["city_text"] = $this->language->get('city_text');
		$data["text_postcode"] = $this->language->get('text_postcode');
		$data["text_code"] = $this->language->get('text_code');
		$data["text_address_1"] = $this->language->get('text_address_1');
		$data["text_edit_password"] = $this->language->get('text_edit_password');
		$data["text_current_password"] = $this->language->get('text_current_password');
		$data["text_new_password"] = $this->language->get('text_new_password');
		$data["text_confirm"] = $this->language->get('text_confirm');
		$data["text_btn_send"] = $this->language->get('text_btn_send');
		$data["text_summ"] = $this->language->get('text_summ');
		$data["text_of"] = $this->language->get('text_of');
		$data["text_total_products"] = $this->language->get('text_total_products');
		$data["text_btn_double_order"] = $this->language->get('text_btn_double_order');
		$data["text_head_post"] = $this->language->get('text_head_post');
		$data["text_fullname"] = $this->language->get('text_fullname');
		$data["text_telephone"] = $this->language->get('text_telephone');
		$data["text_payment"] = $this->language->get('text_payment');
		$data["text_sku"] = $this->language->get('text_sku');
		$data["text_quatity"] = $this->language->get('text_quatity');
		$data["text_price"] = $this->language->get('text_price');
		$data["text_btn_buy"] = $this->language->get('text_btn_buy');
		$data["text_buy_one_click"] = $this->language->get('text_buy_one_click');

		$data['action_edit'] = $this->url->link('account/account', '', true);
		$data['home'] = $this->url->link('common/home');

		$this->response->setOutput($this->load->view('account/account', $data));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function render_pagination($total, $page, $limit, $url, $text_next, $text_prev, $num_links = 8) {


		if ($page < 1) {
			$page = 1;
		}

		if(!(int)$limit){
			$limit = 10;
		}
		$text_first = '|&lt;';
		$text_last = '&gt;|';
		$num_pages = ceil($total / $limit);

		$url = str_replace('%7Bpage%7D', '{page}', $url);

		$output = '<ul class="pagination">';

		if ($page > 1) {

			if ($page === 1) {
				$output .= '<li class="page-item"><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url) . '" class="page-link not-active">' . $text_prev . '</a></li>';
			} else {
				$output .= '<li class="page-item"><a href="' . str_replace('{page}', $page - 1, $url) . '" class="page-link">' . $text_prev . '</a></li>';
			}
		} else {
			$output .= '<li class="page-item"><a href="#"  class="page-link not-active">' . $text_prev . '</a></li>';
		}

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= '<li class="page-item"><a href="#" class="page-link not-active">' . $i . '</a></li>';
				} else {
					if ($i === 1) {
						$output .= '<li class="page-item"><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url) . '" class="page-link">' . $i . '</a></li>';
					} else {
						$output .= '<li class="page-item"><a href="' . str_replace('{page}', $i, $url) . '" class="page-link">' . $i . '</a></li>';
					}
				}
			}
		}

		if ($page < $num_pages) {
			$output .= '<li class="page-item"><a href="' . str_replace('{page}', $page + 1, $url) . '" class="page-link">' . $text_next . '</a></li>';
		} else {
			$output .= '<li class="page-item"><a href="#" class="page-link not-active">' . $text_next . '</a></li>';
		}

		$output .= '</ul>';

		if ($num_pages > 1) {
			return $output;
		} else {
			return '';
		}
	}

	protected function validateForm() {
		$this->load->language('account/account');

		$this->load->model('account/customer');
		$error = [];
		if (utf8_strlen(trim($this->request->post['firstname'])) > 32){
			$error['firstname'] = $this->language->get('error_firstname');
		}

		if (utf8_strlen(trim($this->request->post['lastname'])) > 32) {
			$error['lastname'] = $this->language->get('error_lastname');
		}

		if (trim($this->request->post['email']) != '') {
			if((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
				$error['email'] = $this->language->get('error_email');
			}

		}

		if ($this->customer->getEmail() != trim($this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$error['email'] = $this->language->get('error_exists');
		}

		if (utf8_strlen($this->request->post['telephone']) != 0 && utf8_strlen($this->request->post['telephone']) != 10) {
			$error['telephone'] = $this->language->get('error_telephone');
		}


		if (utf8_strlen(trim($this->request->post['address_1'])) > 0 && (utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
			$error['address_1'] = $this->language->get('error_address_1');
		}

		if (utf8_strlen(trim($this->request->post['city'])) > 0 && ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128))) {
			$error['city'] = $this->language->get('error_city');
		}
		if (utf8_strlen(trim($this->request->post['postcode'])) > 0 && (utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$error['postcode'] = $this->language->get('error_postcode');
		}

		if(trim($this->request->post['current_password']) != '' && trim($this->request->post['new_password']) != '' && $this->request->post['confirm'] != ''){
			$email = $this->customer->getEmail();
			$pwd_sha1 = $this->model_account_customer->getCustomerPassword($email, trim($this->request->post['current_password']));
			if (!$pwd_sha1) {
				$error['current_password'] = $this->language->get('error_password');
			}

			if ((utf8_strlen(html_entity_decode($this->request->post['new_password'], ENT_QUOTES, 'UTF-8')) < 6) || (utf8_strlen(html_entity_decode($this->request->post['new_password'], ENT_QUOTES, 'UTF-8')) > 20)) {
				$error['new_password'] = $this->language->get('new_password');
			}

			if ($this->request->post['confirm'] != $this->request->post['new_password']) {
				$error['confirm'] = $this->language->get('error_confirm');
			}
		} else if(trim($this->request->post['current_password']) != '' || trim($this->request->post['new_password']) != '' || $this->request->post['confirm'] != ''){
			$error['new_password'] = $this->language->get('error_notsend');
		}

		return $error;
	}

	public function wishlist_modal(){
		if (!$this->customer->isLogged()) {
			echo 0;
			return false;
		}
		$this->load->language('account/account');
		$this->load->language('account/wishlist');
		$this->load->model('account/wishlist');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$data['products'] = array();
		$results = $this->model_account_wishlist->getWishlist();
		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
				} else {
					$image = false;
				}
				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}
				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'sku'        => $product_info['sku'],
					'stock'      => $stock,
					'price'      => $price,
					'special'    => $special,
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
				);
				if(count($data["products"]) >= 5){
					$data["msg_continue_btn"] = $this->language->get('msg_continue_btn');
					break;
				}
			}else {
				$this->model_account_wishlist->deleteWishlist($result['product_id']);
			}
		}
		if(empty($data['products'])){
			$data['msg_empty'] = $this->language->get('msg_empty_wishlist');
		}
		$data["wishlist_text"] = $this->language->get('wishlist_text');
		$data['action_continue'] = $this->url->link('account/account', '', true);
		$data["text_sku"] = $this->language->get('text_sku');
		$data["msg_empty_wishlist"] = $this->language->get('msg_empty_wishlist');
		$data["msg_link_continue_wishlist"] = $this->language->get('msg_link_continue_wishlist');
		$data['home'] = $this->url->link('common/home');
		if(!isset($data["msg_continue_btn"])){
			$data["msg_continue_btn"] = $this->language->get('btn_follow_account');
		}

		$this->response->setOutput($this->load->view('account/wishlist_modal', $data));
	}

	public function empty_wishlist_modal(){
		$this->load->language('account/account');
		$this->load->language('account/wishlist');
		$this->load->model('account/wishlist');
		$data['msg_empty'] = $this->language->get('msg_empty_wishlist');
		$data["wishlist_text"] = $this->language->get('wishlist_text');
		$data['action_continue'] = $this->url->link('account/account', '', true);
		$data["text_sku"] = $this->language->get('text_sku');
		$data["msg_empty_wishlist"] = $this->language->get('msg_empty_wishlist');
		$data["msg_link_continue_wishlist"] = $this->language->get('msg_link_continue_wishlist');
		$data['home'] = $this->url->link('common/home');
		$data["msg_continue_btn"] = $this->language->get('btn_follow_account');
		$data['products'] = [];
		$this->response->setOutput($this->load->view('account/wishlist_modal', $data));
	}

}

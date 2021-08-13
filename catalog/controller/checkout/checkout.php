<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->response->redirect($this->url->link('checkout/cart'));

		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->response->redirect($this->url->link('checkout/cart'));
			}
		}

		$this->load->language('checkout/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		// Required by klarna
		if ($this->config->get('payment_klarna_account') || $this->config->get('payment_klarna_invoice')) {
			$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
		$data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 2);
		$data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 2);
		$data['text_checkout_shipping_address'] = sprintf($this->language->get('text_checkout_shipping_address'), 3);
		$data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);

		if ($this->cart->hasShipping()) {
			$method_shipping = $this->method_shipping();
			$data['shipping_methods'] = $method_shipping;
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
		} else {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		$data['logged'] = $this->customer->isLogged();
		$data["city"] = '';
		$data["address_1"] = '';
		$data["postcode"] = '';
		$this->session->data['payment_address'] = array();

		if ($this->customer->isLogged()) {
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
			$data["firstname"] = $customer_info["firstname"];
			$data["lastname"] = $customer_info["lastname"];
			$data["telephone"] = $customer_info["telephone"];
			$data["email"] = $customer_info["email"];
			$this->load->model('account/address');
			if ($customer_info["address_id"] && $this->model_account_address->getAddress($this->customer->getAddressId())) {
				$this->session->data['payment_address'] = $address = $this->model_account_address->getAddress($this->customer->getAddressId());
				$data["city"] = $address["city"];
				$data["address_1"] = $address["address_1"];
				$data["postcode"] = $address["postcode"];
			}

			if ($this->config->get('config_tax_customer') == 'shipping') {
				$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
			}
		} else {
			$data["firstname"] = '';
			$data["lastname"] = '';
			$data["telephone"] = '';
			$data["email"] = '';
		}

		$data["payment_methods"] = $this->method_payment();

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = '';
		}

		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$this->load->model('catalog/product');

		$data['products'] = array();

		$products = $this->cart->getProducts();
		$discount = $total_unit = 0;

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
			} else {
				$image = '';
			}
			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

				$price = $this->currency->format($unit_price, $this->session->data['currency']);
				$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
			}


			$recurring = '';

			if ($product['recurring']) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year')
				);

				if ($product['recurring']['trial']) {
					$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
				}

				if ($product['recurring']['duration']) {
					$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				} else {
					$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
				}
			}

			$product_info = $this->model_catalog_product->getProduct($product['product_id']);
			if (!is_null($product_info['special']) && (float)$product_info['special'] >= 0) {
				$discount_price = (int)$product_info['price'] - (int)$product_info['special'];
				$procent = $this->model_catalog_product->procent_calculate($product_info);
				$discount += $discount_price * $product['quantity'];
			} else {
				$discount_price = $procent = false;
			}
			$total_unit += $product_info['price'] * $product['quantity'];

			$data['products'][] = array(
				'cart_id'   => $product['cart_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'recurring' => $recurring,
				'quantity'  => $product['quantity'],
				'stock'     => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'reward'    => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
				'price'     => $price,
				'total'     => $total,
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'unit_price' => $product_info['price'],
				'sku'        => $product_info['sku'],
				'special'    => $product_info['special'],
				'discount'   => $discount_price,
				'product_id' => $product['product_id'],
				'procent'    => $procent,
			);
		 }

		$data['shipping_required'] = $this->cart->hasShipping();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$total_products = $this->cart->countProducts();
		$data["total_products"] = $total_products;
		$data["register"] = $this->url->link('account/register');
		$data["login"] = $this->url->link('account/login');
		$data["home"] = $this->url->link('common/home');

		$data["heading_title"] = $this->language->get('heading_title');
		$data["recomendation_go_to"] = $this->language->get('recomendation_go_to');
		$data["go_to_account"] = $this->language->get('go_to_account');
		$data["word_or"] = $this->language->get('word_or');
		$data["registration_text"] = $this->language->get('registration_text');
		$data["go_to_products_list"] = $this->language->get('go_to_products_list');
		$data["customer_text"] = $this->language->get('customer_text');
		$data["require_text"] = $this->language->get('require_text');
		$data["products_in_cart"] = $this->language->get('products_in_cart');
		$data["your_order"] = $this->language->get('your_order');
		$data["price_text"] = $this->language->get('price_text');
		$data["count_text"] = $this->language->get('count_text');
		$data["data_shipping"] = $this->language->get('data_shipping');
		$data["writing_data_shipping"] = $this->language->get('writing_data_shipping');
		$data["city_text"] = $this->language->get('city_text');
		$data["address_text"] = $this->language->get('address_text');
		$data["number_post"] = $this->language->get('number_post');
		$data["data_for_pay"] = $this->language->get('data_for_pay');
		$data["select_method_pay"] = $this->language->get('select_method_pay');
		$data["send_order"] = $this->language->get('send_order');
		$data["comment_placeholder"] = $this->language->get('comment_placeholder');
		$data["email_placeholder"] = $this->language->get('email_placeholder');
		$data["telephone_placeholder"] = $this->language->get('telephone_placeholder');
		$data["your_lastname"] = $this->language->get('your_lastname');
		$data["your_firstname"] = $this->language->get('your_firstname');

		$this->response->setOutput($this->load->view('checkout/checkout', $data));
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

	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function method_shipping(){
		$method_data = array();

		$this->load->model('setting/extension');

		$results = $this->model_setting_extension->getExtensions('shipping');

		foreach ($results as $result) {
			if ($this->config->get('shipping_' . $result['code'] . '_status')) {
				$this->load->model('extension/shipping/' . $result['code']);

				$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote('');

				if ($quote) {
					$method_data[$result['code']] = array(
						'title'      => $quote['title'],
						'quote'      => $quote['quote'],
						'sort_order' => $quote['sort_order'],
						'error'      => $quote['error']
					);
				}
			}
		}

		$sort_order = array();

		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $method_data);
		$this->session->data['shipping_methods'] = $method_data;
		return $method_data;
	}

	public function method_payment(){
		// Payment Methods
$method_data = array();

$this->load->model('setting/extension');

$results = $this->model_setting_extension->getExtensions('payment');

$recurring = $this->cart->hasRecurringProducts();

foreach ($results as $result) {
	if ($this->config->get('payment_' . $result['code'] . '_status')) {
		$total = 0;
		$this->load->model('extension/payment/' . $result['code']);

		$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

		if ($method) {
			if ($recurring) {
				if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
					$method_data[$result['code']] = $method;
				}
			} else {
				$method_data[$result['code']] = $method;
			}
		}
	}
}

$sort_order = array();

foreach ($method_data as $key => $value) {
	$sort_order[$key] = $value['sort_order'];
}

array_multisort($sort_order, SORT_ASC, $method_data);

$this->session->data['payment_methods'] = $method_data;
return $method_data;
	}
}

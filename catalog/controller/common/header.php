<?php

class ControllerCommonHeader extends Controller {

	public function index() {

		// Analytics

		$this->load->model('setting/extension');



		$data['analytics'] = array();



		$analytics = $this->model_setting_extension->getExtensions('analytics');



		foreach ($analytics as $analytic) {

			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {

				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));

			}

		}



		if ($this->request->server['HTTPS']) {

			$server = $this->config->get('config_ssl');

		} else {

			$server = $this->config->get('config_url');

		}



		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {

			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');

		}



		$data['title'] = $this->document->getTitle();



		$data['base'] = $server;

		$data['description'] = $this->document->getDescription();

		$data['keywords'] = $this->document->getKeywords();

		$data['links'] = $this->document->getLinks();

		$data['styles'] = $this->document->getStyles();

		$data['scripts'] = $this->document->getScripts('header');

		$data['lang'] = $this->language->get('code');

		$data['direction'] = $this->language->get('direction');



		$data['name'] = $this->config->get('config_name');



		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {

			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');

		} else {

			$data['logo'] = '';

		}



		$this->load->language('common/header');


		// Current url
		
		$data['_route_'] = $this->request->get['_route_'];


		// Wishlist

		if ($this->customer->isLogged()) {

			$this->load->model('account/wishlist');



			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());

		} else {

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));

		}



		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));



		$data['home'] = $this->url->link('common/home');

		$data['wishlist'] = $this->url->link('account/wishlist', '', true);

		$data['logged'] = $this->customer->isLogged();

		$data['account'] = $this->url->link('account/account', '', true);

		$data['register'] = $this->url->link('account/register', '', true);

		$data['login'] = $this->url->link('account/login', '', true);

		$data['order'] = $this->url->link('account/order', '', true);

		$data['transaction'] = $this->url->link('account/transaction', '', true);

		$data['download'] = $this->url->link('account/download', '', true);

		$data['logout'] = $this->url->link('account/logout', '', true);

		$data['shopping_cart'] = $this->url->link('checkout/cart');

		$data['checkout'] = $this->url->link('checkout/checkout', '', true);

		$data['contact'] = $this->url->link('information/contact');

		$data['telephone'] = $this->config->get('config_telephone');

		$data['address'] = nl2br($this->config->get('config_address'));

		//$data['language'] = $this->load->controller('common/language');

		$data['currency'] = $this->load->controller('common/currency');

		$data['search'] = $this->load->controller('common/search');

		$data['cart'] = $this->load->controller('common/cart');

		$data['menu'] = $this->load->controller('common/menu');



		//return $this->load->view('common/header', $data);



        $tree_cats = $this->load->controller('extension/module/tree_cats');



                $data["tree_cats"] = $tree_cats;

				$canonical = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				if(isset($this->request->get['gclid'] ) || isset($this->request->get['utm_medium'] ) || isset($this->request->get['utm_source'] ) || isset($this->request->get['utm_campaign'] ) || isset($this->request->get['utm_content'] ) || isset($this->request->get['utm_term'] ) || isset($this->request->get['_openstat'] )){
					
					$pageURL = (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
    				$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
					$canonical = explode( '?', $pageURL )[0];
				}
				$data['canonical'] = $canonical;
				//echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];


                if($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php?route=common/home'){

                    return $this->load->view('common/test', $data);

                } else {

                    return $this->load->view('common/headerpage', $data);

                }

	}

}


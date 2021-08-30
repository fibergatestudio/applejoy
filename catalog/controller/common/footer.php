<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

        $this->load->language('extension/module/tree_cats');

        $this->load->model('catalog/tree_cats');


        $categories = $this->model_catalog_tree_cats->getTreeCats();

        foreach($categories as $id => $category){
            $categories[$id]['href'] = $this->url->link('product/category', 'path=' . $id);
        }

        $category_tree = $this->model_catalog_tree_cats->getMapTree($categories);


        $data["cat_tree"] = $category_tree;

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['repairs_href'] = $this->url->link('custompage/repairs');
		$data['blog_href'] = $this->url->link('custompage/blog');

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['home'] = $this->url->link('common/home');
		$data['link_register'] = $this->url->link('account/register');
		$data['link_login'] = $this->url->link('account/login');

		$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles('footer');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['communication_and_questions'] = $this->language->get('communication_and_questions');
		$data['accept_payment'] = $this->language->get('accept_payment');
		$data['text_products'] = $this->language->get('text_products');
		$data['text_services'] = $this->language->get('text_services');
		$data['text_information'] = $this->language->get('text_information');
		$data['order_is_placed'] = $this->language->get('order_is_placed');
		$data['thanks_for_order'] = $this->language->get('thanks_for_order');
		$data['text_msg_admin'] = $this->language->get('text_msg_admin');
		$data['continue_markets'] = $this->language->get('continue_markets');
		$data['shopping_cart'] = $this->language->get('shopping_cart');
		$data['empty_cart'] = $this->language->get('empty_cart');
		$data['return_to_shopping'] = $this->language->get('return_to_shopping');
		$data['buy_in_1_click'] = $this->language->get('buy_in_1_click');
		$data['your_telephone_for_communication'] = $this->language->get('your_telephone_for_communication');
		$data['label_for_input_telephone'] = $this->language->get('label_for_input_telephone');
		$data['confirm_consent'] = $this->language->get('confirm_consent');
		$data['send_order'] = $this->language->get('send_order');
		$data['product_in_cart'] = $this->language->get('product_in_cart');
		$data['product_add_to_cart'] = $this->language->get('product_add_to_cart');
		$data['go_to_cart'] = $this->language->get('go_to_cart');
		$data['mini_cart'] = $this->language->get('mini_cart');
		$data['text_registration'] = $this->language->get('text_registration');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_login_socials'] = $this->language->get('text_login_socials');
		$data['word_or'] = $this->language->get('word_or');
		$data['go_to_registration'] = $this->language->get('go_to_registration');
		$data['have_account'] = $this->language->get('have_account');
		$data['have_not_account'] = $this->language->get('have_not_account');
		$data['i_agree_on'] = $this->language->get('i_agree_on');
		$data['personal_data_processing'] = $this->language->get('personal_data_processing');
		$data['forgot_passwd'] = $this->language->get('forgot_passwd');
		$data['your_first_name'] = $this->language->get('your_first_name');
		$data['repeat_passwd'] = $this->language->get('repeat_passwd');
		$data['your_name'] = $this->language->get('your_name');
		$data['validate_passwd'] = $this->language->get('validate_passwd');
		$data['password_recovery'] = $this->language->get('password_recovery');
		$data['enter_email'] = $this->language->get('enter_email');
		$data['text_send'] = $this->language->get('text_send');
		$data['new_passwd'] = $this->language->get('new_passwd');
		$data['validate_new_passwd'] = $this->language->get('validate_new_passwd');
		$data['text_continue'] = $this->language->get('text_continue');
		$data['text_vacansion'] = $this->language->get('text_vacansion');
		$data['text_review'] = $this->language->get('text_review');
		$data['your_telephone'] = $this->language->get('your_telephone');
		$data['text_passwd'] = $this->language->get('text_passwd');
		$data['repairs'] = $this->language->get('repairs');
		$data['text_blog'] = $this->language->get('text_blog');
		$data['your_login'] = $this->language->get('your_login');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['telephone'] = nl2br($this->config->get('config_telephone'));
		$data["tel_link"] = preg_replace('/[^0-9]/', '', nl2br($this->config->get('config_telephone')));
		$data['open'] = nl2br($this->config->get('config_open'));

		return $this->load->view('common/footer', $data);
	}
}

<?php
class ControllerExtensionModuleTreeCats extends Controller {
	public function index() {
		$this->load->language('extension/module/tree_cats');
    $data['btn_contact'] = $this->language->get('contacts');
		$data['btn_all_products'] = $this->language->get('all_products');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (!empty($parts)) {
			$data['active'] = end($parts);
		} else {
			$data['active'] = 0;
		}

		$this->load->model('catalog/tree_cats');

		$categories = $this->model_catalog_tree_cats->getTreeCats();

		foreach($categories as $id => $category){
			$categories[$id]['href'] = $this->url->link('product/category', 'path=' . $id);
		}

		$category_tree = $this->model_catalog_tree_cats->getMapTree($categories);

		$data["categories_tree"] = $category_tree;

		$subcategories = [];
		$grandson = [];
		foreach($category_tree as $id => $category_item){
			if(isset($category_item["childs"])){
				$subcategories[$id] = $category_item["childs"];
				foreach($category_item["childs"] as $sub_id => $item_subchild){
					if(isset($item_subchild["childs"])){
						$grandson[$sub_id] = $item_subchild["childs"];
					}
				}
			}
		}

		$quant = $this->cart->countProducts();
		$data['text_items_quantity'] = $quant;

		$data['subcategories'] = $subcategories;
		$data['grandson_cat'] = $grandson;

		$data['home'] = $this->url->link('common/home');
		$data['contact'] = $this->url->link('information/contact');
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['language'] = $this->load->controller('common/language');
		$data['mobil_lang'] = $this->load->controller('common/mobil_lang');

		$data['address'] = nl2br($this->config->get('config_address'));
		$data['telephone'] = nl2br($this->config->get('config_telephone'));
		$data['open'] = nl2br($this->config->get('config_open'));

		$data['text_login'] = $this->language->get('text_login');
		$data['text_registration'] = $this->language->get('text_registration');
		$data['picking'] = $this->language->get('picking');
		$data['repairs'] = $this->language->get('repairs');
		$data['text_blog'] = $this->language->get('text_blog');
		$data['text_vacansion'] = $this->language->get('text_vacansion');
		$data['text_trade_in'] = $this->language->get('text_trade_in');
		$data['text_review'] = $this->language->get('text_review');
		$data['logged'] = $this->customer->isLogged();
		$data['text_logout'] = $this->language->get('text_logout');
		$data['href_logout'] = $this->url->link('account/logout');
		$data['text_account'] = $this->language->get('text_account');
		$data['href_account'] = $this->url->link('account/account');


		$data['telephone'] = nl2br($this->config->get('config_telephone'));
		$data["tel_link"] = preg_replace('/[^0-9]/', '', nl2br($this->config->get('config_telephone')));
		$data['open'] = nl2br($this->config->get('config_open'));
		$data['vacancies_href'] = $this->url->link('custompage/vacancies');
		$data['feedback_href'] = $this->url->link('information/feedback');
		$data['trade_in_href'] = $this->url->link('information/tradein');
		$data['repairs_href'] = $this->url->link('repair/repair');
		$data['blog_href'] = $this->url->link('extension/blog/blog_list');

		// Search
		$data['search_home'] = $this->load->controller('common/search');

		return $this->load->view('extension/module/cats_template', $data);


	}


}


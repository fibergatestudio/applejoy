<?php
class ControllerExtensionModuleTreeCats extends Controller {
	public function index() {
		$this->load->language('extension/module/tree_cats');
    $data['test_module'] = 'modele test!!!';

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

		return $this->load->view('extension/module/cats_template', $data);


	}


}

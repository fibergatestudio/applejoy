<?php
class ControllerExtensionModuleViewed extends Controller {
	public function index() {
		$this->load->language('extension/module/viewed');
    $data['test_module'] = 'modele test!!!';
		if(!isset($_SESSION)){
			session_start();
		}
    if(!isset($_SESSION["viewed"]) || empty($_SESSION["viewed"])){
      return '';
    }
    $arr_id_product = $_SESSION["viewed"];

    $this->load->model('catalog/product');
    $this->load->model('tool/image');

    $products_viewed = [];


    foreach($arr_id_product as $id_item_product){
      $product_info_viewed = $this->model_catalog_product->getProduct($id_item_product);

      if ($product_info_viewed) {
        if ($product_info_viewed['image']) {
  				$image = $this->model_tool_image->resize($product_info_viewed['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
  			} else {
  				$image = '';
  			}

        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
          $price = $this->currency->format($this->tax->calculate($product_info_viewed['price'], $product_info_viewed['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
        } else {
          $price = false;
        }

        if (!is_null($product_info_viewed['special']) && (float)$product_info_viewed['special'] >= 0) {
          $special = $this->currency->format($this->tax->calculate($product_info_viewed['special'], $product_info_viewed['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
          $tax_price = (float)$product_info_viewed['special'];
        } else {
          $special = false;
          $tax_price = (float)$product_info_viewed['price'];
        }

        if ($this->config->get('config_tax')) {
          $tax = $this->currency->format($tax_price, $this->session->data['currency']);
        } else {
          $tax = false;
        }

        if ($this->config->get('config_review_status')) {
          $rating = $product_info_viewed['rating'];
        } else {
          $rating = false;
        }

        $data['products_viewed'][] = array(
          'product_id'  => $product_info_viewed['product_id'],
          'thumb'       => $image,
          'name'        => $product_info_viewed['name'],
          'description' => utf8_substr(strip_tags(html_entity_decode($product_info_viewed['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
          'price'       => $price,
          'special'     => $special,
          'tax'         => $tax,
          'rating'      => $rating,
          'minimum'     => $product_info_viewed['minimum'] > 0 ? $product_info_viewed['minimum'] : 1,
          'href'        => $this->url->link('product/product', 'product_id=' . $product_info_viewed['product_id']),

        );
      }
    }
		return $this->load->view('extension/module/viewed', $data);
	}


}

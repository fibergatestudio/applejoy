<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}
		$this->load->language('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['popular_products'] = $this->language->get('popular_products');
		$data['mobil_banners_iphone'] = $this->language->get('mobil_banners_iphone');
		$data['mobil_banners_MacBook'] = $this->language->get('mobil_banners_MacBook');
		$data['mobil_banners_Apple_Watch'] = $this->language->get('mobil_banners_Apple_Watch');
		$data['mobil_banners_accesuares'] = $this->language->get('mobil_banners_accesuares');
		$data['mobil_banners_use_iPhone'] = $this->language->get('mobil_banners_use_iPhone');
		$data['mobil_banners_use_MacBook'] = $this->language->get('mobil_banners_use_MacBook');
		$data['desctop_banners'] = $this->language->get('desctop_banners');
		$data['motivational_banner'] = $this->language->get('motivational_banner');
		$data['tarrget_buttons'] = $this->language->get('tarrget_buttons');
		$data['content_action'] = $this->language->get('content_action');
		$data['your_email'] = $this->language->get('your_email');
		$data['read_details'] = $this->language->get('read_details');
		$data['subscribe'] = $this->language->get('subscribe');

		$data['repairs'] = $this->url->link('custompage/repairs');

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}

<?php
class ControllerCustompageTradeIn extends Controller {
	private $error = array();

	public function index() {

		// Load language var
		$this->load->language('custompage/tradein');

		// Page title
		$this->document->setTitle($this->language->get('heading_title'));

		// Breadcrumbs data
		// Home link
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		// 
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('custompage/trade-in')
		);

		// Include header/footer
		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('custompage/tradein', $data));
	}

}

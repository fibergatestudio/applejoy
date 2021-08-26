<?php
class ControllerRepairProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('repair/repair');
		$this->load->model('repair/repair');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['product'] = $this->model_repair_repair->getProductByID($this->request->get['id']);
		//echo '<pre>';
		//var_dump($data['product']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('repair/repair')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'). ' '. $data['category_name'] ,
			'href' => $this->url->link('repair/category')
		);

		$data['faq'] = $this->load->controller('repair/faq');

		$data['modal'] = $this->load->controller('extension/module/repair');
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$data['categories'] = $this->model_repair_repair->getCategoriesMock();

		$this->response->setOutput($this->load->view('repair/product', $data));
	}


}

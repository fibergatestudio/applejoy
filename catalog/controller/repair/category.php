<?php
class ControllerRepairCategory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('repair/repair');
		$this->load->model('repair/repair');

		$this->document->setTitle($this->language->get('heading_title'));
		$data['categories'] = $this->model_repair_repair->getCategoriesMock();

		$category_id = $this->request->get['id'];

		$data['categories'][$category_id]['active'] = 'active';

		$data['category_name'] = $data['categories'][$category_id]['name'];

		//$data['models'] = $data['categories'][$category_id]['models'];

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
			//'href' => $this->url->link('repair/repair')
		);
		$data['modal'] = $this->load->controller('extension/module/repair');
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$data['faq'] = $this->load->controller('repair/faq');

		$this->response->setOutput($this->load->view('repair/category', $data));
	}


}

<?php
class ControllerRepairRepair extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('repair/repair');
		$this->load->model('repair/repair');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('repair/repair')
		);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		
		$data['faq'] = $this->load->controller('repair/faq');
		
		$data['modal'] = $this->load->controller('extension/module/repair');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$data['categories'] = $this->model_repair_repair->getCategoriesMock();

		$this->response->setOutput($this->load->view('repair/repair', $data));
	}

}

<?php
class ControllerExtensionModuleRepair extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/repair');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/repair');
		$this->model_extension_module_repair->createRepairOrder();

		$this->getList();
	}

	protected function getList() {

		$this->load->language('extension/module/repair');
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('heading_title');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['column_name'] = $this->language->get('column_name');

		$this->load->model('extension/module/repair');
		$result = $this->model_extension_module_repair->getRepairOrder();
		$data['newsltr'] = array();
		foreach($result as $res)
		{
			$data['newsltr'][] = array(
				'id' => $res['id'],
				'date'=> $res['date'],
				'name' => $res['name'],
				'phone' => $res['phone'],
				'product' => $res['product'],
				'message' => $res['message'],
				'delete' => $this->url->link('extension/module/repair/delete', 'user_token=' . $this->session->data['user_token'] . '&id='.$res['id'], true)
			);
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$pagination = new Pagination();
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/repair', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/repair', $data));
	}

	public function delete() {
		if (isset($this->request->get['id'])) {
			$id = $this->request->get['id'];
		} else {
			$id = 0;
		}
		$this->load->model('extension/module/repair');
		$this->model_extension_module_repair->deleteRepairOrder($id);
		$this->session->data['success'] = 'Deleted';
		$this->response->redirect($this->url->link('extension/module/repair', 'user_token=' . $this->session->data['user_token'], true));
	}
	

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/repair')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
				
		return !$this->error;
	}
	
	public function install() {
		$this->load->model('extension/module/repair');
		$this->model_extension_module_repair->createDatabaseTable();
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('repair', ['module_repair_status' => 1]);
	}

	public function uninstall() {
		$this->load->model('extension/module/repair');
		$this->model_extension_module_repair->dropDatabaseTable();
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('repair');
	}
	
}
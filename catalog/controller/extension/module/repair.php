<?php
class ControllerExtensionModuleRepair extends Controller {

	public function index($setting) {
		$this->load->language('repair/repair');
		$this->load->model('extension/module/repair');
		$data = [];

		return $this->load->view('extension/module/repair', $data);
	}


	public function addOrder() {
		$post = $this->request->post;

		$product = $post['product'];
		$product .= $post['model'];

		$message = $post['issue'];
		$message .= $post['message'];

		$data = [
			'name' => $post['name'],
			'phone' => $post['phone'],
			'product' => $product,
			'message' => $message,
		];

		$json = array();
		if (!ctype_digit($data['phone']) || utf8_strlen($data['phone']) != 10) {
			$json['error']['phoneInput'] = 'Error: not phone!';
		}
		if (!isset($json['error'])) {
			$this->load->model('extension/module/repair');
			$this->model_extension_module_repair->addOrder($data);
			$json['success'] = true;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


}

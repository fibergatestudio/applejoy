<?php
class ControllerExtensionModuleNewsletter extends Controller {
	public function index($setting) {
		static $module = 0;
		$this->load->language('extension/module/newsletter');
		
		$data['entry_email'] = $this->language->get('entry_email');
		$data['button_subscribe'] = $this->language->get('button_subscribe');
		$data['button_unsubscribe'] = $this->language->get('button_unsubscribe');

		$data['type'] = $setting['type'];
		$data['delay'] = $setting['delay'];
		$data['only_once'] = $setting['only_once'];
		$data['unsubscribe'] = $setting['unsubscribe'];

		$this->document->addScript('catalog/view/javascript/jquery/jquery.cookie.js');

		if ($setting['type'] == 'popup') {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/newsletter.css');
		}

		if (!empty($setting['module_description'][$this->config->get('config_language_id')]['success_message'])) {
		$data['success_message'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['success_message'], ENT_QUOTES, 'UTF-8');
		} else {
		$data['success_message'] = $this->language->get('message_subscribed');
		}

		if(empty($setting['module_description'][$this->config->get('config_language_id')]['title'])) {
			$data['heading_title'] = '';
		} else if (isset($setting['module_description'][$this->config->get('config_language_id')]['title'])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
		}

		if(empty($setting['module_description'][$this->config->get('config_language_id')]['description'])) {
			$data['html'] = '';
		} else if ($setting['module_description'][$this->config->get('config_language_id')]['description'] == '&lt;p&gt;&lt;br&gt;&lt;/p&gt;') {
			$data['html'] = '';
		} else {
			$data['html'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
		}

		if(empty($setting['image'])) {
			$data['image'] = false;
		} else if (isset($setting['image'])) {
			$data['image'] = 'image/' . $setting['image'];
		}


		$data['module'] = $module++;

		return $this->load->view('extension/module/newsletter', $data);

	}


	public function subscribe() {

	$this->load->language('extension/module/newsletter');

		$json = array();
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
				$json['error'] = $this->language->get('error_email');
			}
			if (!isset($json['error'])) {
				$this->load->model('extension/module/newsletter');
			if($this->model_extension_module_newsletter->checkRegistered($this->request->post)){
			   	$this->model_extension_module_newsletter->UpdateRegistered($this->request->post,1);
				$json['success'] = true;
			} else if ($this->model_extension_module_newsletter->checkExist($this->request->post)){
				$json['error'] = $this->language->get('message_exist');
			} else {
				$this->model_extension_module_newsletter->subscribe($this->request->post);
				$json['success'] = true;
		   	}
		  }
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	public function unsubscribe() {

	$this->load->language('extension/module/newsletter');

		$json = array();
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
				$json['error'] = $this->language->get('error_email');
			}
			if (!isset($json['error'])) {
				$this->load->model('extension/module/newsletter');
			if($this->model_extension_module_newsletter->checkRegistered($this->request->post)){
			   	$this->model_extension_module_newsletter->UpdateRegistered($this->request->post,0);
				$json['success'] = $this->language->get('message_unsubscribed');
			} else if (!$this->model_extension_module_newsletter->checkExist($this->request->post)){
				$json['error'] = $this->language->get('message_not_found');
			} else {
				$this->model_extension_module_newsletter->unsubscribe($this->request->post);
				$json['success'] = $this->language->get('message_unsubscribed');
		   	}
		  }
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


}

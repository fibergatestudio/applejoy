<?php
class ControllerExtensionModuleNewsletter extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/newsletter');

		$this->document->setTitle($this->language->get('page_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('newsletter', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_module_type'] = $this->language->get('text_module_type');
		$data['text_popup'] = $this->language->get('text_popup');
		
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['help_title'] = $this->language->get('help_title');
		$data['entry_success_message'] = $this->language->get('entry_success_message');
		$data['help_success_message'] = $this->language->get('help_success_message');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['help_description'] = $this->language->get('help_description');
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['entry_type'] = $this->language->get('entry_type');
		$data['help_type'] = $this->language->get('help_type');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['help_image'] = $this->language->get('help_image');
		$data['entry_delay'] = $this->language->get('entry_delay');
		$data['help_delay'] = $this->language->get('help_delay');
		$data['entry_unsubscribe'] = $this->language->get('entry_unsubscribe');
		$data['entry_only_once'] = $this->language->get('entry_only_once');
		$data['help_only_once'] = $this->language->get('help_only_once');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('page_title'),
				'href' => $this->url->link('extension/module/newsletter', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('page_title'),
				'href' => $this->url->link('extension/module/newsletter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);			
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/newsletter', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/newsletter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);
	
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}
		
		$this->load->model('tool/image');
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		if (isset($this->request->post['image'])) {
		$data['image'] = $this->request->post['image'];
		} elseif (!empty($module_info)) {
		$data['image'] = $module_info['image'];
		} else {
		$data['image'] = '';
		}
		
		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
		$data['icon'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (isset($module_info['image']) && is_file(DIR_IMAGE . $module_info['image'])) {
		$data['icon'] = $this->model_tool_image->resize($module_info['image'], 100, 100);
		} else {
		$data['icon'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
			
		if (isset($this->request->post['module_description'])) {
			$data['module_description'] = $this->request->post['module_description'];
		} elseif (!empty($module_info)) {
			$data['module_description'] = $module_info['module_description'];
		} else {
			$data['module_description'] = array();
		}
		
		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($module_info)) {
			$data['type'] = $module_info['type'];
		} else {
			$data['type'] = '';
		}
		
		if (isset($this->request->post['delay'])) {
			$data['delay'] = $this->request->post['delay'];
		} elseif (!empty($module_info)) {
			$data['delay'] = $module_info['delay'];
		} else {
			$data['delay'] = '0';
		}
		
		if (isset($this->request->post['only_once'])) {
			$data['only_once'] = $this->request->post['only_once'];
		} elseif (!empty($module_info)) {
			$data['only_once'] = $module_info['only_once'];
		} else {
			$data['only_once'] = '';
		}
		
		
		if (isset($this->request->post['unsubscribe'])) {
			$data['unsubscribe'] = $this->request->post['unsubscribe'];
		} elseif (!empty($module_info)) {
			$data['unsubscribe'] = $module_info['unsubscribe'];
		} else {
			$data['unsubscribe'] = '';
		}
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/newsletter', $data));
	}
	

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/newsletter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
				
		return !$this->error;
	}
	
	public function install() {
		$this->load->model('extension/module/newsletter');
		$this->model_extension_module_newsletter->createDatabaseTable();
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'sale/subscriber');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'sale/subscriber');
	}

	public function uninstall() {
		$this->load->model('extension/module/newsletter');
		$this->model_extension_module_newsletter->dropDatabaseTable();
	}
	
}
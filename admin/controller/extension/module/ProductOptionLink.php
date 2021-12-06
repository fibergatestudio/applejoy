<?php
// *	ProductOptionLink


class ControllerExtensionModuleProductOptionLink extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/ProductOptionLink');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_ProductOptionLink', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/ProductOptionLink', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/ProductOptionLink', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/ProductOptionLink', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);


		// Статус модуля
		if (isset($this->request->post['module_ProductOptionLink_status'])) {
			$data['module_ProductOptionLink_status'] = $this->request->post['module_ProductOptionLink_status'];
		} else {
			$data['module_ProductOptionLink_status'] = $this->config->get('module_ProductOptionLink_status');
		}

		// Статус по умолчанию Вывод опций
		if (isset($this->request->post['module_ProductOptionLink_link_view'])) {
			$data['module_ProductOptionLink_link_view'] = $this->request->post['module_ProductOptionLink_link_view'];
		} else {
			$data['module_ProductOptionLink_link_view'] = $this->config->get('module_ProductOptionLink_link_view');
		}

		// Статус по умолчанию названия
		if (isset($this->request->post['module_ProductOptionLink_on_off_name'])) {
			$data['module_ProductOptionLink_on_off_name'] = $this->request->post['module_ProductOptionLink_on_off_name'];
		} else {
			$data['module_ProductOptionLink_on_off_name'] = $this->config->get('module_ProductOptionLink_on_off_name');
		}

		// Статус по умолчанию изображения
		if (isset($this->request->post['module_ProductOptionLink_on_off_images'])) {
			$data['module_ProductOptionLink_on_off_images'] = $this->request->post['module_ProductOptionLink_on_off_images'];
		} else {
			$data['module_ProductOptionLink_on_off_images'] = $this->config->get('module_ProductOptionLink_on_off_images');
		}

		// Статус полной проверки
		if (isset($this->request->post['module_ProductOptionLink_on_off_fullcheck'])) {
			$data['module_ProductOptionLink_on_off_fullcheck'] = $this->request->post['module_ProductOptionLink_on_off_fullcheck'];
		} else {
			$data['module_ProductOptionLink_on_off_fullcheck'] = $this->config->get('module_ProductOptionLink_on_off_fullcheck');
		}

		// Статус пакетного сохранения 
		if (isset($this->request->post['module_ProductOptionLink_on_off_save'])) {
			$data['module_ProductOptionLink_on_off_save'] = $this->request->post['module_ProductOptionLink_on_off_save'];
		} else {
			$data['module_ProductOptionLink_on_off_save'] = $this->config->get('module_ProductOptionLink_on_off_save');
		}
		
		// Статус вывода при отутствии товара
		if (isset($this->request->post['module_ProductOptionLink_on_off_stock'])) {
			$data['module_ProductOptionLink_on_off_stock'] = $this->request->post['module_ProductOptionLink_on_off_stock'];
		} else {
			$data['module_ProductOptionLink_on_off_stock'] = $this->config->get('module_ProductOptionLink_on_off_stock');
		}

		// Размер изображения длина
		if (isset($this->request->post['module_ProductOptionLink_resize_length'])) {
			$data['module_ProductOptionLink_resize_length'] = $this->request->post['module_ProductOptionLink_resize_length'];
		} else {
			$data['module_ProductOptionLink_resize_length'] = $this->config->get('module_ProductOptionLink_resize_length');
		}

		// Размер изображения высота
		if (isset($this->request->post['module_ProductOptionLink_resize_height'])) {
			$data['module_ProductOptionLink_resize_height'] = $this->request->post['module_ProductOptionLink_resize_height'];
		} else {
			$data['module_ProductOptionLink_resize_height'] = $this->config->get('module_ProductOptionLink_resize_height');
		}

		// Стили Новые в одном массиве
		if (isset($this->request->post['module_ProductOptionLink_style'])) {
			$data['module_ProductOptionLink_style'] = $this->request->post['module_ProductOptionLink_style'];
		} else {
			$data['module_ProductOptionLink_style'] = $this->config->get('module_ProductOptionLink_style');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/ProductOptionLink', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/ProductOptionLink')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
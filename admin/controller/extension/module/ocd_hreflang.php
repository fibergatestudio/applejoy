<?php

class ControllerExtensionModuleOcdHreflang extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('extension/module/ocd_hreflang');

        $this->document->setTitle($this->language->get('heading_main_title'));

        $this->load->model('setting/setting');
        $this->load->model('extension/module/ocd_hreflang');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('ocd_hreflang', $this->request->post);

            if (isset($this->request->post['ocd_hreflang_url'])) {
                $this->model_extension_module_ocd_hreflang->setSeoUrl($this->request->post['ocd_hreflang_url']);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/ocd_hreflang', 'user_token=' . $this->session->data['user_token'], true));
        }

        if (isset($this->error['hreflang'])) {
            $data['error_hreflang'] = $this->error['hreflang'];
        } else {
            $data['error_hreflang'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/ocd_hreflang', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $module_info = $this->config->get('ocd_hreflang');
        
        if (isset($this->request->post['ocd_hreflang'])) {
            $data['ocd_hreflang'] = $this->request->post['ocd_hreflang'];
        } elseif (!empty($module_info)) {
            $data['ocd_hreflang'] = $module_info;
        } else {
            $data['ocd_hreflang'] = [];
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        $data['ocd_hreflang_url'] = $this->model_extension_module_ocd_hreflang->getSeoUrl();

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['ocd_hreflang_data'])) {
            $data['ocd_hreflang_data'] = $this->request->post['ocd_hreflang_data'];
        } elseif ($this->config->get('ocd_hreflang_data')) {
            $data['ocd_hreflang_data'] = $this->config->get('ocd_hreflang_data');
        } else {
            $data['ocd_hreflang_data'] = [];
        }
        
        $data['action'] = $this->url->link('extension/module/ocd_hreflang', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/ocd_hreflang', $data));
    }

    public function install() {
        $this->load->language('extension/extension/ocd_hreflang');

        $this->load->model('setting/extension');

        if (!$this->user->hasPermission('modify', 'extension/module/ocd_hreflang')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/ocd_hreflang');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/ocd_hreflang');

        $this->session->data['success'] = $this->language->get('text_success');
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/ocd_hreflang')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        $data = $this->request->post['ocd_hreflang'];

        foreach ($languages as $language) {
            if ((utf8_strlen($data[$language['language_id']]) < 2) || (utf8_strlen($data[$language['language_id']]) > 32)) {
                $this->error['hreflang'] = $this->language->get('error_hreflang');
            }
        }

        return !$this->error;
    }
}
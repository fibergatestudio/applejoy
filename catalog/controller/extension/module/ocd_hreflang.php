<?php

class ControllerExtensionModuleOcdHreflang extends Controller {

    public function index() {
        $this->load->model('localisation/language');

        $result = [];

        $languages = $this->model_localisation_language->getLanguages();

        $module_info = $this->config->get('ocd_hreflang');

        foreach ($languages as $language) {
            if ($language['status']) {
                $this->config->set('config_language_id', $languages[$language['code']]['language_id']);
            }

            if (!isset($this->request->get['route'])) {
                $href = $this->url->link('common/home');
            } else {
                $url_data = $this->request->get;

                unset($url_data['_route_']);

                $route = $url_data['route'];

                unset($url_data['route']);

                $url = '';

                if ($url_data) {
                    $url = '&' . urldecode(http_build_query($url_data, '', '&'));
                }

                $protocol = $this->request->server['HTTPS'];

                if (isset($route) && isset($url) && isset($protocol)) {
                    $href = $this->url->link($route, $url, $protocol);
                } else {
                    $href = $this->url->link('common/home');
                }
            }

            if (isset($module_info[$language['language_id']])) {
                $result[] = array(
                    'code' => $module_info[$language['language_id']],
                    'href' => $href,
                    'rel'  => 'alternate',
                );
            }
        }

        $this->load->model('extension/module/ocd_hreflang');

        $session_language_id = $this->model_extension_module_ocd_hreflang->getLanguageByCode($this->session->data['language']);

        if ($session_language_id) {
            $this->config->set('config_language_id', $session_language_id);
        }

        return $result;
    }
}
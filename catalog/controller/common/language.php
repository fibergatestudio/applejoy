<?php

class ControllerCommonLanguage extends Controller {

	public function index() {

		$this->saveLanguage();

		$this->load->language('common/language');



		$data['action'] = $this->url->link('common/language/language', '', $this->request->server['HTTPS']);



		$data['code'] = $this->session->data['language'];


		$this->load->model('localisation/language');



		$data['languages'] = array();



		$results = $this->model_localisation_language->getLanguages();



		foreach ($results as $result) {

			if ($result['status']) {

				$data['languages'][] = array(

					'name' => $result['name'],

					'code' => $result['code']

				);

			}

		}


		if (!isset($this->request->get['route'])) {

			$data['redirect'] = $this->url->link('common/home');

		} else {

			$url_data = $this->request->get;



			unset($url_data['_route_']);



			$route = $url_data['route'];



			unset($url_data['route']);



			$url = '';



			if ($url_data) {

				$url = '&' . urldecode(http_build_query($url_data, '', '&'));

			}


			$cur_url = explode('/', $this->url->link($route, $url, $this->request->server['HTTPS']) );
			if ( $this->session->data['language'] == 'ru-ru' ) {
				unset($cur_url[0],$cur_url[1],$cur_url[2],$cur_url[3]);
			}
			else
			{
				unset($cur_url[0],$cur_url[1],$cur_url[2]);
			}
			$data['cur_url'] = '/'.implode('/', $cur_url);

			$data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);

		}

		return $this->load->view('common/language', $data);

	}

	/**
	 * Function save language user
	 * @return $this->response->redirect() | false
	 */
	public function saveLanguage()
	{
		// Host http - https
		$curr_host = $this->request->server['HTTPS'] ? 'https://'.$_SERVER['HTTP_HOST'].'/' : 'http://'.$_SERVER['HTTP_HOST'];
		// This Url empty home
		$current_url = isset($this->request->get['_route_']) ? $this->request->get['_route_'] : '';
		// cookie save lang
		$save_lang = isset( $this->request->cookie['language_save'] ) ? $this->request->cookie['language_save'] : null;
		// active session lang
		$curr_lang = $this->session->data['language'];

		if ($save_lang && $save_lang != $curr_lang)
		{
			if($save_lang == 'ru-ru')
			{
				$this->response->redirect($curr_host.'/ru/'.$current_url);
			}
			elseif ( $save_lang == 'uk-ua' )
			{
				$this->response->redirect($curr_host.'/'.$current_url);
			}
		}

		return false;

	}


	public function language()
	{

		if (isset($this->request->post['code'])) {

			$this->session->data['language'] = $this->request->post['code'];

			// null save langue
			setcookie('language_save', null, -1, '/', $this->request->server['HTTP_HOST']);
			// new cookie save langue
			setcookie('language_save', $this->request->post['code'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);

		}



		if (isset($this->request->post['redirect'])) {

			$this->response->redirect($this->request->post['redirect']);

		} else {

			$this->response->redirect($this->url->link('common/home'));

		}

	}

}


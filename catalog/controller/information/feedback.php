<?php

class ControllerInformationFeedback extends Controller {


	private $error = array();


	public function index() {
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		$this->load->language('information/feedback');

		$this->document->setTitle($this->language->get('heading_title'));


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(


			'text' => $this->language->get('text_home'),


			'href' => $this->url->link('common/home')


		);


		$data['breadcrumbs'][] = array(


			'text' => $this->language->get('heading_title'),


			'href' => $this->url->link('information/feedback')


		);


		$this->load->model('catalog/information');


		$data['comments'] = array();


		foreach ($this->model_catalog_information->getComments($page) as $result) {


			$data['comments'][] = array(


				'author_name' => $result['author_name'],

				'image'  => $result['image'],

				'date'  => $result['date'],

				'comment'  => $result['comment'],


			);


		}


		$data['column_left'] = $this->load->controller('common/column_left');


		$data['column_right'] = $this->load->controller('common/column_right');


		$data['content_top'] = $this->load->controller('common/content_top');


		$data['content_bottom'] = $this->load->controller('common/content_bottom');


		$data['footer'] = $this->load->controller('common/footer');


		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/feedback', $data));


		
	}


}



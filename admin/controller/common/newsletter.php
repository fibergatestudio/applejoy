<?php
class ControllerCommonNewsletter extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/newsletter');

		$this->document->setTitle($this->language->get('page_title'));

		$this->load->model('common/newsletter');
		$this->model_common_newsletter->createNewsletter();

		$this->getList();
	}

	protected function getList() {

		$this->load->language('extension/module/newsletter');
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('heading_title');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['column_name'] = $this->language->get('column_name');

		$this->load->model('common/newsletter');
		$result = $this->model_common_newsletter->getNewsLetter();
		$data['newsltr'] = array();
		foreach($result as $res)
		{
			$data['newsltr'][] = array(
				'id' => $res['id'],
				'email' => $res['email'],
				'delete' => $this->url->link('common/newsletter/delete', 'user_token=' . $this->session->data['user_token'] . '&id='.$res['id'], true)
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
		$pagination->url = $this->url->link('common/newsletter', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/newsletter', $data));
	}

	public function delete() {
		if (isset($this->request->get['id'])) {
			$id = $this->request->get['id'];
		} else {
			$id = 0;
		}
		$this->load->model('common/newsletter');
		$this->model_common_newsletter->deleteNewsletter($id);
		$this->session->data['success'] = 'Deleted';
		$this->response->redirect($this->url->link('common/newsletter', 'user_token=' . $this->session->data['user_token'], true));
	}

}	
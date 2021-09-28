<?php
class ControllerCustompageTradeIn extends Controller {
	private $error = array();

/**
 * Index function page
 */
	public function index() {

		// Load language var
		$this->load->language('custompage/tradein');

		// Page title
		$this->document->setTitle($this->language->get('heading_title'));

		// Breadcrumbs data
		// Home link
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		// Trade-in link
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('custompage/trade-in')
		);

		// Include header/footer
		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('custompage/tradein', $data));
	}

	/**
	 * Function validate
	 */
	protected function validate()
	{

	}

	/**
	 * Function from submit contact form page
	 */
	public function sendContact()
	{
		$post_request = $this->request->post;
		$json = [];
		// input
		$email = trim($post["contacts"]);
	}

}

<?php
class ControllerCustompageVacancies extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('custompage/vacancies');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

    $this->load->model('extension/module/vacancies');
    $this->load->model('tool/image');
    $session_lang = $this->session->data['language'];
		$cur_lang_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "language l WHERE l.code = '" . $session_lang . "'");
		$lang_rows = $cur_lang_data->rows[0];
		$lang_id = $lang_rows["language_id"];
    $vacancies_start = $this->model_extension_module_vacancies->getLastVacancies($lang_id);
    $vacancies = [];
    foreach ($vacancies_start as $item_vacancy) {
      $select = $item_vacancy["vacancy_image"];
      $item_vacancy["vacancy_image"] = $this->model_tool_image->resize($select, 320, 222);
      $vacancies[] = $item_vacancy;
    }
    $data["vacancies"] = $vacancies;

    $data['text_header_page'] = $this->language->get('text_header_page');
		$data['head_form_subscribe'] = $this->language->get('head_form_subscribe');
		$data['your_full_name'] = $this->language->get('your_full_name');
		$data['your_contact_text'] = $this->language->get('your_contact_text');
		$data['your_contact_placeholder'] = $this->language->get('your_contact_placeholder');
		$data['your_comment'] = $this->language->get('your_comment');
		$data['comment_placeholder'] = $this->language->get('comment_placeholder');
		$data['text_button_send'] = $this->language->get('text_button_send');


		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		 $this->response->setOutput($this->load->view('custompage/vacancies', $data));
	}

	protected function validate() {
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['enquiry']) < 10) || (utf8_strlen($this->request->post['enquiry']) > 3000)) {
			$this->error['enquiry'] = $this->language->get('error_enquiry');
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		return !$this->error;
	}

	public function addSubscribe(){
		$post = $this->request->post;
		$json = [];
		$email = trim($post["contacts"]);
		$full_name = trim($post["fullname"]);
		$comment = trim($post["comment"]);
		$chars = [' ','-', '_'];
		$telephone = str_replace($chars, '', $email);
		if ((filter_var($email, FILTER_VALIDATE_EMAIL) || (ctype_digit($telephone) && utf8_strlen($telephone) == 10)) &&
	        utf8_strlen($full_name) > 8){
						$data = [
							'contact' => $email,
							'fullname' => $full_name,
							'comment' => $comment
						];
						$this->load->model('extension/module/vacancies');
						$id = $this->model_extension_module_vacancies->saveSubscribe($data);
						if($id){
							$json['success'] = $id;
						} else {
							$json['db_error'] = 'ERROR DB';
						}

		} else {
			$json['error'] = [];
			if(!filter_var($email, FILTER_VALIDATE_EMAIL) && (!ctype_digit($telephone) || utf8_strlen($telephone) != 10)){
				$json['error']['mailInput1'] = 'Error: not email';
			}
			if(utf8_strlen($full_name) < 8){
				$json['error']['nameInput1'] = 'Not format name';
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

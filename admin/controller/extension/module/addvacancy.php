<?php
class ControllerExtensionModuleAddvacancy extends Controller {

	private $error = array();

  public function install() {
    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."vacancies` (
      `vacancy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `title` text NOT NULL,
      `short_description` text NOT NULL,
      `price` varchar(40),
      `vacancy_image` text NOT NULL,
      `description` text NOT NULL,
      `meta_title` varchar(40) NOT NULL,
      `meta_description` text NOT NULL,
      `meta_keywords` varchar(40) NOT NULL,
      `slug` varchar(50) NOT NULL,
      `status` enum('Published','Draft','Trash') NOT NULL DEFAULT 'Draft',
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `modified_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`vacancy_id`)
    )");

    $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."vacancy_translate` (
      `vacancy_translate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `vacancy_id` int(11) NOT NULL,
      `language_id` int(11) NOT NULL,
      `vacancy_title` text NOT NULL,
      `vacancy_description` text NOT NULL,
      `vacancy_short_description` text NOT NULL,
      PRIMARY KEY (`vacancy_translate_id`)
    )");

		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."challenger` (
			`challenger_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`fullname` text NOT NULL,
			`contact` text NOT NULL,
			`comment` text,
			`send_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`status` enum('consider','answer','trash') NOT NULL DEFAULT 'consider',
			PRIMARY KEY (`challenger_id`)
		)");

  }


	public function index() {
		$this->load->language('extension/module/addvacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_addvacancy', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
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
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/addvacancy', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/addvacancy', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_addvacancy_status'])) {
			$data['module_addvacancy_status'] = $this->request->post['module_addvacancy_status'];
		} else {
			$data['module_addvacancy_status'] = $this->config->get('module_addvacancy_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/addvacancy', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/addvacancy')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function lists(){
		$this->load->language('extension/module/addvacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_addvacancy', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
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
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_addvacancy_status'])) {
			$data['module_blog_list_status'] = $this->request->post['module_blog_list_status'];
		} else {
			$data['module_blog_list_status'] = $this->config->get('module_blog_list_status');
		}

		$data['add'] = $this->url->link('extension/module/addvacancy/createVacancy', 'user_token=' . $this->session->data['user_token'], true);
		$data['copy'] = $this->url->link('extension/module/addvacancy/copyVacancy', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('extension/module/addvacancy/trashVacancy', 'user_token=' . $this->session->data['user_token'], true);
		$data['challengerList'] = $this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token'], true);

		$data['vacancies'] = array();

		$limit= 10;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;

		$this->load->model('extension/module/vacancy');
		$totalvacancies = $this->model_extension_module_vacancy->getVacanciesCount();

		$data['vacancies'] = $this->model_extension_module_vacancy->getVacancies($start, $limit);

		$vacancy_info = $data['vacancies'];

		$this->load->model('tool/image');

		foreach ($vacancy_info as $key => $value) {

			if (is_file(DIR_IMAGE . $vacancy_info[$key]['vacancy_image'])) {
				$vacancy_info[$key]['vacancy_image'] = $this->model_tool_image->resize($vacancy_info[$key]['vacancy_image'], 40, 40);
			}
			else {
				$vacancy_info[$key]['vacancy_image'] = $this->model_tool_image->resize('not_vacancy.jpg', 40, 40);
			}
		}

		$data['vacancies'] = $vacancy_info;

		$data['user_token'] = $this->session->data['user_token'];

		//pagination
			$pagination = new Pagination();
			$pagination->total = $totalvacancies;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
		$data['pagination'] = $pagination->render();
		//current records status
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalvacancies) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalvacancies - $limit)) ? $totalvacancies : ((($page - 1) * $limit) + $limit), $totalvacancies, ceil($totalvacancies / $limit));

		$data['post_restore_url'] = $this->url->link('extension/module/addvacancy/restoreVacancy', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['empty_trash_url'] = $this->url->link('extension/module/addvacancy/emptyTrash', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['vacancy_edit_url'] = $this->url->link('extension/module/addvacancy/editVacancy', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data["column_image"] = $this->language->get('featured_image');
		$data["text_title"] = $this->language->get('text_title');
		$data["entry_status"] = $this->language->get('entry_status');
		$data["short_description"] = $this->language->get('short_description');
		$data["date_publish"] = $this->language->get('date_publish');
		$data["title_trash"] = $this->language->get('title_trash');
		$data["challenger_link_text"] = $this->language->get('challenger_link_text');
		$data["text_empty_list"] = $this->language->get('text_empty_list');
		$data['action_trash'] = $this->url->link('extension/module/addvacancy/totrashVacancy', 'user_token=' . $this->session->data['user_token'], true);

		$this->response->setOutput($this->load->view('extension/module/module_vacancies_list', $data));
	}

	public function createVacancy(){
		$this->load->language('extension/module/addvacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_create_vacancy'),
			'href' => $this->url->link('extension/module/addvacancy/createVacancy', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];


		$this->load->model('localisation/language');
		$language_total = $this->model_localisation_language->getLanguages();
		$data["language_vacancy"] = [];
		foreach($language_total as $code => $item_lang){
			$data["language_vacancy"][$code] = [];
			$data["language_vacancy"][$code]['code'] = $code;
			$data["language_vacancy"][$code]['language_id'] = $item_lang["language_id"];
		}


		$data['action_create'] = $this->url->link('extension/module/addvacancy/saveVacancy', 'user_token=' . $this->session->data['user_token'], true);

		$data['title'] = $this->language->get('text_title');
		$data['short_desc'] = $this->language->get('short_description');
		$data['price_text'] = $this->language->get('price_text');
		$data['plsholder_price'] = $this->language->get('plsholder_price');
		$data['description'] = $this->language->get('text_description');
		$data['featured_image'] = $this->language->get('featured_image');
		$data['meta_title'] = $this->language->get('meta_title');
		$data['meta_description'] = $this->language->get('meta_description');
		$data['meta_keywords'] = $this->language->get('meta_keywords');
		$data['vacancy_slug'] = $this->language->get('vacancy_slug');
		$data['text_draft'] = $this->language->get('text_draft');
		$data['text_trash'] = $this->language->get('text_trash');
		$data['entry_status'] = $this->language->get('entry_status');

		$this->load->model('tool/image');
		$data['thumb'] = $this->model_tool_image->resize('not_vacancy.jpg', 100, 100);

		$data['cancel'] = $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('extension/module/vacancy');
		$data["heading_blog_create_title"] = $this->language->get('text_create_vacancy');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['text_create_vacancy'] = $this->url->link('extension/module/addvacancy/saveVacancy', 'user_token=' . $this->session->data['user_token'], true);

		$this->response->setOutput($this->load->view('extension/module/create_vacancy' , $data));

	}

	public function copyVacancy(){
		$get = $this->request->get;
		$id = $get['vacancy_id'];
		$this->load->model('extension/module/vacancy');
		$vacancy_data = $this->model_extension_module_vacancy->getVacancy($id);
		$new_vacancy = $vacancy_data[0];
		$new_vacancy["status"] = "Draft";
		$translate_arr_0 = $this->model_extension_module_vacancy->getTranslate($id);
		$translate_arr = [];
		foreach ($translate_arr_0 as $translt) {
			if(!isset($translate_arr[$translt["language_id"]])){
				$translate_arr[$translt["language_id"]] = [];
			}
			$translate_arr[$translt["language_id"]]["language_id"] = $translt["language_id"];
			$translate_arr[$translt["language_id"]]["vacancy_title"] = $translt["vacancy_title"];
			$translate_arr[$translt["language_id"]]["vacancy_description"] = $translt["vacancy_description"];
			$translate_arr[$translt["language_id"]]["vacancy_short_desc"] = $translt["vacancy_short_description"];
		}
		$id_copy = $this->model_extension_module_vacancy->savePost($new_vacancy, $translate_arr);
		$this->response->redirect($this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function editVacancy(){
		// echo 'Edit';
		$this->load->model('tool/image');
		$this->load->language('extension/module/addvacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_blog_mgmt', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_edit_post'),
			'href' => $this->url->link('extension/module/blog_mgmt/editPost', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['module_addvacancy_status'])) {
			$data['module_addvacancy_status'] = $this->request->post['module_addvacancy_status'];
		} else {
			$data['module_addvacancy_status'] = $this->config->get('module_addvacancy_status');
		}

		/* custom codes starts here */

		$get = $this->request->get;
		$id = $get['vacancy_id'];
		$this->load->model('extension/module/vacancy');
		$vacancy_data = $this->model_extension_module_vacancy->getVacancy($id);
		$data['status'] = $vacancy_data[0]['status'];
		$data['vacancy'] = $vacancy_data[0];
		$selected_image = $data['vacancy'];

		$data['thumb'] = $this->model_tool_image->resize($selected_image['vacancy_image'], 100, 100);

		if(empty($data['thumb'])){
			$data['thumb'] = $this->model_tool_image->resize('not_vacancy.png', 100, 100);
		}
		// Translate custum

		$this->load->model('localisation/language');
		$language_total = $this->model_localisation_language->getLanguages();
		$data["language_article"] = [];

		foreach($language_total as $code => $item_lang){
			$translate_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancy_translate WHERE vacancy_id=" . $id. " AND language_id=".$item_lang["language_id"]);
			$data["language_vacancy"][$code] = [];
			if(!empty($translate_data->row)){
				$item_taranslate = $translate_data->row;
				$data["language_vacancy"][$code]['code'] = $code;
				$data["language_vacancy"][$code]['language_id'] = $item_lang["language_id"];
				$data["language_vacancy"][$code]['vacancy_title'] = $item_taranslate["vacancy_title"];
				$data["language_vacancy"][$code]['vacancy_description'] = $item_taranslate["vacancy_description"];
				$data["language_vacancy"][$code]['vacancy_short_description'] = $item_taranslate["vacancy_short_description"];
				$data["language_vacancy"][$code]['vacancy_translate_id'] = $item_taranslate["vacancy_translate_id"];
			} else {
				$item_taranslate = $translate_data->row;
				$data["language_vacancy"][$code]['code'] = $code;
				$data["language_vacancy"][$code]['language_id'] = $item_lang["language_id"];
				$data["language_vacancy"][$code]['vacancy_title'] = $vacancy_data[0]["title"];//$item_taranslate["article_title"];
				$data["language_vacancy"][$code]['vacancy_description'] = $vacancy_data[0]["description"];//$item_taranslate["article_description"];
				$data["language_vacancy"][$code]['vacancy_short_description'] = $vacancy_data[0]["short_description"];
				$data["language_vacancy"][$code]['vacancy_translate_id'] = 0;//$item_taranslate["article_translate_id"];
			}

		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data["text_title"] = $this->language->get('text_title');
		$data["text_description"] = $this->language->get('text_description');
		$data["short_description"] = $this->language->get('short_description');
		$data["text_edit"] = $this->language->get('text_edit');
		$data["text_copy"] = $this->language->get('text_copy');
		$data["text_to_trash"] = $this->language->get('text_to_trash');

		$data['action_edit'] = $this->url->link('extension/module/addvacancy/updateVacancy', 'user_token=' . $this->session->data['user_token'], true);

		$this->response->setOutput($this->load->view('extension/module/edit_vacancy' , $data));
	}

	public function updateVacancy(){
		$id = $this->request->post['id'];
		$temp_post = $this->request->post;

		$curr_lang_id = $this->config->get('config_language_id');
		$translate_arr = $add_arr = [];
		foreach($temp_post as $name => $item_post){
			if(strpos($name, 'title') !== false && $name != 'title' && $name != 'meta_title'){
				$id_lang = str_replace('title', '', $name);
				$arr_id = explode('-', $id_lang);
				if((int)$arr_id[1] != 0 && isset($translate_arr[$arr_id[1]])){
					$translate_arr[$arr_id[1]]["vacancy_title"] = $item_post;
				} else if((int)$arr_id[1] != 0){
					$translate_arr[$arr_id[1]] = [];
					$translate_arr[$arr_id[1]]["vacancy_title"] = $item_post;
					$translate_arr[$arr_id[1]]["language_id"] = $arr_id[0];
					$translate_arr[$arr_id[1]]["vacancy_id"] = $id;
				} else {
					if(isset($add_arr[$arr_id[0]])){
						$add_arr[$arr_id[0]]["vacancy_title"] = $item_post;
						$add_arr[$arr_id[0]]["language_id"] = $arr_id[0];
						$add_arr[$arr_id[0]]["vacancy_id"] = $id;
					} else {
						$add_arr[$arr_id[0]] = [];
						$add_arr[$arr_id[0]]["vacancy_title"] = $item_post;
						$add_arr[$arr_id[0]]["language_id"] = $arr_id[0];
						$add_arr[$arr_id[0]]["vacancy_id"] = $id;
					}
				}
				if($arr_id[0] == $curr_lang_id){
					$title = $item_post;
				}
			}
			if(strpos($name, 'description') !== false && $name != 'description' && $name != 'meta_description'){
				$id_lang = str_replace('description', '', $name);
				$arr_id = explode('-', $id_lang);
				if((int)$arr_id[1] != 0 && isset($translate_arr[$arr_id[1]])){
					$translate_arr[$arr_id[1]]["vacancy_description"] = $item_post;
				} else if((int)$arr_id[1] != 0){
					$translate_arr[$arr_id[1]] = [];
					$translate_arr[$arr_id[1]]["vacancy_description"] = $item_post;
					$translate_arr[$arr_id[1]]["language_id"] = $arr_id[0];
					$translate_arr[$arr_id[1]]["vacancy_id"] = $id;
				} else {
					if(isset($add_arr[$arr_id[0]])){
						$add_arr[$arr_id[0]]["vacancy_description"] = $item_post;
						$add_arr[$arr_id[0]]["language_id"] = $arr_id[0];
						$add_arr[$arr_id[0]]["vacancy_id"] = $id;
					} else {
						$add_arr[$arr_id[0]] = [];
						$add_arr[$arr_id[0]]["vacancy_description"] = $item_post;
						$add_arr[$arr_id[0]]["language_id"] = $arr_id[0];
						$add_arr[$arr_id[0]]["vacancy_id"] = $id;
					}
				}
				if($arr_id[0] == $curr_lang_id){
					$description = $item_post;
				}
			}
			if(strpos($name, 'short_desc') !== false){
				$id_lang = str_replace('short_desc', '', $name);
				$arr_id = explode('-', $id_lang);
				if((int)$arr_id[1] != 0 && isset($translate_arr[$arr_id[1]])){
					$translate_arr[$arr_id[1]]["vacancy_short_description"] = $item_post;
				} else if((int)$arr_id[1] != 0){
					$translate_arr[$arr_id[1]] = [];
					$translate_arr[$arr_id[1]]["vacancy_short_description"] = $item_post;
					$translate_arr[$arr_id[1]]["language_id"] = $arr_id[0];
					$translate_arr[$arr_id[1]]["vacancy_id"] = $id;
				} else {
					if(isset($add_arr[$arr_id[0]])){
						$add_arr[$arr_id[0]]["vacancy_short_description"] = $item_post;
						$add_arr[$arr_id[0]]["language_id"] = $arr_id[0];
						$add_arr[$arr_id[0]]["vacancy_id"] = $id;
					} else {
						$add_arr[$arr_id[0]] = [];
						$add_arr[$arr_id[0]]["vacancy_short_description"] = $item_post;
						$add_arr[$arr_id[0]]["language_id"] = $arr_id[0];
						$add_arr[$arr_id[0]]["vacancy_id"] = $id;
					}
				}
				if($arr_id[0] == $curr_lang_id){
					$short_description = $item_post;
				}
			}
		}

		$image = trim($this->request->post['image']) != '' ? trim($this->request->post['image']) : 'not_vacancy.jpg';
		$price = trim($this->request->post['price']) != '' ? trim($this->request->post['price']) : '0';
		$meta_title= $this->request->post['meta_title'];
		$meta_description= $this->request->post['meta_description'];
		$meta_keywords= $this->request->post['meta_keywords'];
		$status = $this->request->post['status'];

		// add Slug
		$slug= $this->request->post['slug'];
		$slug = preg_replace('/[^A-Za-z0-9\-]/', '-', $slug);
		$slug = preg_replace('/-+/', '-', $slug);

		$data = array(
			'title' 			=> $title,
			'description' 		=> $description,
			'short_description' 		=> $short_description,
			'price' => $price,
			'vacancy_image' 		=> $image,
			'meta_title' 		=>	$meta_title,
			'meta_description' 	=>	$meta_description,
			'meta_keywords' 	=>	$meta_keywords,
			'slug' 				=>	$slug,
			'status'			=> $status
		);

		$this->load->model('extension/module/vacancy');
		$data['vacancy'] = $this->model_extension_module_vacancy->updateVacancy($data,$id);
		$this->model_extension_module_vacancy->updateTranslate($translate_arr, $id);
      if(!empty($add_arr)){
				$this->model_extension_module_vacancy->addVacancyTranslate($add_arr,$id);
			}
		$this->response->redirect($this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function totrashVacancy(){
		$get = $this->request->get;
		$id = $get['vacancy_id'];
		$this->load->model('extension/module/vacancy');
		$vacancy_data = $this->model_extension_module_vacancy->getVacancy($id);
		$new_vacancy = $vacancy_data[0];
		$new_vacancy["status"] = "Trash";
		$this->model_extension_module_vacancy->updateVacancy($new_vacancy, $id);
		$this->response->redirect($this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function saveVacancy(){
		$temp_post = $this->request->post;
		$curr_lang_id = $this->config->get('config_language_id');
		$translate_arr = [];
		foreach($temp_post as $name => $item_post){
			if(strpos($name, 'title') !== false && $name != 'title' && $name != 'meta_title'){
				$id_lang = str_replace('title', '', $name);
				if(isset($translate_arr[$id_lang])){
					$translate_arr[$id_lang]["vacancy_title"] = $item_post;
				} else {
					$translate_arr[$id_lang] = [];
					$translate_arr[$id_lang]["vacancy_title"] = $item_post;
				}
				if($id_lang == $curr_lang_id){
					$title = $item_post;
				}
			}
			if(strpos($name, 'description') !== false && $name != 'description' && $name != 'meta_description'){
				$id_lang = str_replace('description', '', $name);
				if(isset($translate_arr[$id_lang])){
					$translate_arr[$id_lang]["vacancy_description"] = $item_post;
				} else {
					$translate_arr[$id_lang] = [];
					$translate_arr[$id_lang]["vacancy_description"] = $item_post;
				}
				if($id_lang == $curr_lang_id){
					$description = $item_post;
				}
			}
			if(strpos($name, 'short_desc') !== false){
				$id_lang = str_replace('short_desc', '', $name);
				if(isset($translate_arr[$id_lang])){
					$translate_arr[$id_lang]["vacancy_short_desc"] = $item_post;
				} else {
					$translate_arr[$id_lang] = [];
					$translate_arr[$id_lang]["vacancy_short_desc"] = $item_post;
				}
				if($id_lang == $curr_lang_id){
					$short_desc = $item_post;
				}
			}
		}

		$image = trim($this->request->post['image']) != '' ? trim($this->request->post['image']) : 'not_vacancy.jpg';
		$price = trim($this->request->post['price']) != '' ? trim($this->request->post['price']) : '0';
		$status = $this->request->post['status'];
		$meta_title= $this->request->post['meta_title'];
		$meta_description= $this->request->post['meta_description'];
		$meta_keywords= $this->request->post['meta_keywords'];
		// add Slug
		$slug = $this->request->post['slug'];
		$slug = preg_replace('/[^A-Za-z0-9\-]/', '-', $slug);
		$slug = preg_replace('/-+/', '-', $slug);
		$data_save = array(
		'title' 			=> $title,
		'description' 		=> htmlspecialchars($description),
		'short_description' => $short_desc,
		'price' => $price,
		'vacancy_image'		=> $image,
		'meta_title' 		=>	$meta_title,
		'meta_description' 	=>	$meta_description,
		'meta_keywords' 	=>	$meta_keywords,
		'slug' 				=>	$slug,
		'status'			=> $status
		);
		$this->load->model('extension/module/vacancy');
		$data['last_vacancy_id'] = $last_vacancy_id = $this->model_extension_module_vacancy->savePost($data_save, $translate_arr);
	$this->response->redirect($this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function trashVacancy(){
		$this->load->language('extension/module/addvacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_addvacancy', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('title_trash'),
			'href' => ''
		);

		if (isset($this->request->post['module_addvacancy_status'])) {
			$data['module_blog_list_status'] = $this->request->post['module_blog_list_status'];
		} else {
			$data['module_blog_list_status'] = $this->config->get('module_blog_list_status');
		}

		$data['vacancies'] = array();

		$limit= 10;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;

		$this->load->model('extension/module/vacancy');
		$totalvacancies = $this->model_extension_module_vacancy->getTrashVacanciesCount();

		$data['vacancies'] = $this->model_extension_module_vacancy->getTrashVacancies($start, $limit);

		$vacancy_info = $data['vacancies'];

		$this->load->model('tool/image');

		foreach ($vacancy_info as $key => $value) {

			if (is_file(DIR_IMAGE . $vacancy_info[$key]['vacancy_image'])) {
				$vacancy_info[$key]['vacancy_image'] = $this->model_tool_image->resize($vacancy_info[$key]['vacancy_image'], 40, 40);
			}
			else {
				$vacancy_info[$key]['vacancy_image'] = $this->model_tool_image->resize('not_vacancy.jpg', 40, 40);
			}
		}

		$data['vacancies'] = $vacancy_info;

		$data['user_token'] = $this->session->data['user_token'];

		//pagination
			$pagination = new Pagination();
			$pagination->total = $totalvacancies;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
		$data['pagination'] = $pagination->render();

		//current records status
		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalvacancies) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalvacancies - $limit)) ? $totalvacancies : ((($page - 1) * $limit) + $limit), $totalvacancies, ceil($totalvacancies / $limit));

		$data['vacancy_restore_url'] = $this->url->link('extension/module/addvacancy/restoreVacancy', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['empty_trash_url'] = $this->url->link('extension/module/addvacancy/emptyTrash', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['vacancy_delete_url'] = $this->url->link('extension/module/addvacancy/deleteVacancy', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data["column_image"] = $this->language->get('featured_image');
		$data["text_title"] = $this->language->get('text_title');
		$data["entry_status"] = $this->language->get('entry_status');
		$data["short_description"] = $this->language->get('short_description');
		$data["date_publish"] = $this->language->get('date_publish');
		$data['action_trash'] = $this->url->link('extension/module/addvacancy/totrashVacancy', 'user_token=' . $this->session->data['user_token'], true);
		$data["title_trash"] = $this->language->get('title_trash');
		$data["text_delete"] = $this->language->get('text_delete');
		$data["text_restore"] = $this->language->get('text_restore');
		$data["text_empty_trash"] = $this->language->get('text_empty_trash');

		$this->response->setOutput($this->load->view('extension/module/trash_vacancy', $data));
	}

	public function restoreVacancy(){
		$get = $this->request->get;
		$id = $get['vacancy_id'];
		$this->load->model('extension/module/vacancy');
		$vacancy_data = $this->model_extension_module_vacancy->getVacancy($id);
		$new_vacancy = $vacancy_data[0];
		$new_vacancy["status"] = "Draft";
		$this->model_extension_module_vacancy->updateVacancy($new_vacancy, $id);
		$this->response->redirect($this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function deleteVacancy(){
		$get = $this->request->get;
		$id = $get['vacancy_id'];
		$this->load->model('extension/module/vacancy');
		$this->model_extension_module_vacancy->deleteVacancy($id);
		$this->response->redirect($this->url->link('extension/module/addvacancy/trashVacancy', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function emptyTrash(){
		$result = $this->db->query("SELECT vacancy_id FROM " . DB_PREFIX . "vacancies WHERE `status` = 'Trash'");
		$arr_delete = $result->rows;
		$this->load->model('extension/module/vacancy');
		foreach($arr_delete as $del){
			$this->model_extension_module_vacancy->deleteVacancy($del["vacancy_id"]);
		}
		$this->response->redirect($this->url->link('extension/module/addvacancy/trashVacancy', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function challengerList(){
		$this->load->language('extension/module/addvacancy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_addvacancy', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('challenger_link_text'),
			'href' => ''
		);

		$this->load->model('extension/module/vacancy');

		$limit= 10;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;

		$totalchallenger = $this->model_extension_module_vacancy->getChallengerCount();

		$pretendents = $this->model_extension_module_vacancy->getPretendent($start, $limit);

		foreach ($pretendents as $key => $value) {
			$pretendents[$key]['status'] = $this->language->get($value['status']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		//pagination
			$pagination = new Pagination();
			$pagination->total = $totalchallenger;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
		  $data['pagination'] = $pagination->render();

    $data['action_trash_chall'] = $this->url->link('extension/module/addvacancy/totrashClallenger', 'user_token=' . $this->session->data['user_token'], true);
		$data['challng_edit_url'] = $this->url->link('extension/module/addvacancy/editClallenger', 'user_token=' . $this->session->data['user_token'], true);
		$data['trash_url'] = $this->url->link('extension/module/addvacancy/trashChallencher', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['pretendents'] = $pretendents;
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['head_fullname'] = $this->language->get('head_fullname');
		$data['head_contact'] = $this->language->get('head_contact');
		$data['head_send_at'] = $this->language->get('head_send_at');

		$this->response->setOutput($this->load->view('extension/module/list_challenger' , $data));
	}

	public function editClallenger(){
		$this->load->language('extension/module/addvacancy');
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
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('challenger_link_text'),
			'href' => $this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('edit_chall_text'),
			'href' => ''
		);
		$get = $this->request->get;
		$id = $get['challenger_id'];
		$this->load->model('extension/module/vacancy');
		$vacancy_data = $this->model_extension_module_vacancy->getChallenger($id);
		$vacancy_data = $vacancy_data[0];
		$vacancy_data['status_text'] = $this->language->get($vacancy_data['status']);
		$data["pretendent"] = $vacancy_data;

		$data["text_consider"] = $this->language->get('consider');
		$data["text_answer"] = $this->language->get('answer');
		$data["head_fullname"] = $this->language->get('head_fullname');
		$data["head_contact"] = $this->language->get('head_contact');
		$data["head_send_at"] = $this->language->get('head_send_at');
		$data["head_comment"] = $this->language->get('head_comment');

		// TO DELETE
		//$data['action_trash_chall'] = $this->url->link('extension/module/addvacancy/totrashClallenger', 'user_token=' . $this->session->data['user_token'], true);
		$data['trash_url'] = $this->url->link('extension/module/addvacancy/trashChallencher', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['action_edit'] = $this->url->link('extension/module/addvacancy/updateChallencher', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->response->setOutput($this->load->view('extension/module/edit_challenger' , $data));
	}

	public function updateChallencher(){
		$temp_post = $this->request->post;
		$id = $temp_post['challenger_id'];
		$status = $temp_post['status'];
		$this->load->model('extension/module/vacancy');
		$vacancy_data = $this->model_extension_module_vacancy->updateChallenger($id, $status);
		$this->response->redirect($this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function totrashClallenger(){
			$get = $this->request->get;
			$id = $get['challenger_id'];
			$this->load->model('extension/module/vacancy');
			$vacancy_data = $this->model_extension_module_vacancy->updateChallenger($id, 'trash');
			$this->response->redirect($this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token'], true));
		}

		public function trashChallencher(){
			$this->load->language('extension/module/addvacancy');

			$this->document->setTitle($this->language->get('heading_title'));

			$this->load->model('setting/setting');

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				$this->model_setting_setting->editSetting('module_addvacancy', $this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
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
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/addvacancy/lists', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('challenger_link_text'),
				'href' => $this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('title_trash'),
				'href' => ''
			);

			$this->load->model('extension/module/vacancy');

			$limit= 10;
			$page=isset($this->request->get['page'])?$this->request->get['page']:1;
			$start= ($page - 1) * $limit;

			$totalchallenger = $this->model_extension_module_vacancy->getTrashChallengerCount();

			$pretendents = $this->model_extension_module_vacancy->getTrashPretendent($start, $limit);

			foreach ($pretendents as $key => $value) {
				$pretendents[$key]['status'] = $this->language->get($value['status']);
			}

			$data['user_token'] = $this->session->data['user_token'];

			//pagination
				$pagination = new Pagination();
				$pagination->total = $totalchallenger;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->url = $this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token']  . '&page={page}', true);
			  $data['pagination'] = $pagination->render();

	    $data['action_trash_chall'] = $this->url->link('extension/module/addvacancy/totrashClallenger', 'user_token=' . $this->session->data['user_token'], true);
			$data['challng_edit_url'] = $this->url->link('extension/module/addvacancy/editClallenger', 'user_token=' . $this->session->data['user_token'], true);
			$data['trash_url'] = $this->url->link('extension/module/addvacancy/trashChallencher', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
			$data['pretendent_restore_url'] = $this->url->link('extension/module/addvacancy/restoreClallenger', 'user_token=' . $this->session->data['user_token'], true);
			$data['chalenger_delete_url'] = $this->url->link('extension/module/addvacancy/deleteClallenger', 'user_token=' . $this->session->data['user_token'], true);
			$data['empty_trash_url'] = $this->url->link('extension/module/addvacancy/emptyTrashClallenger', 'user_token=' . $this->session->data['user_token'], true);

			$data['pretendents'] = $pretendents;
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$data['head_fullname'] = $this->language->get('head_fullname');
			$data['head_contact'] = $this->language->get('head_contact');
			$data['head_send_at'] = $this->language->get('head_send_at');

			$this->response->setOutput($this->load->view('extension/module/list_trash_challenger' , $data));
		}

		public function restoreClallenger(){
			$get = $this->request->get;
			$id = $get['challenger_id'];
			$this->load->model('extension/module/vacancy');
			$vacancy_data = $this->model_extension_module_vacancy->updateChallenger($id, 'consider');
			$this->response->redirect($this->url->link('extension/module/addvacancy/challengerList', 'user_token=' . $this->session->data['user_token'], true));
		}

		public function deleteClallenger(){
			$get = $this->request->get;
			$id = $get['challenger_id'];
			$this->db->query("DELETE FROM " . DB_PREFIX . "challenger WHERE challenger_id = '" . (int)$id . "'");
			$this->response->redirect($this->url->link('extension/module/addvacancy/trashChallencher', 'user_token=' . $this->session->data['user_token'], true));
		}

		public function emptyTrashClallenger(){
			$query = $this->db->query("SELECT challenger_id FROM " . DB_PREFIX . "challenger WHERE `status` = 'trash'");
			$arr_del = $query->rows;
			foreach ($arr_del as $row) {
				$id = $row["challenger_id"];
				$this->db->query("DELETE FROM " . DB_PREFIX . "challenger WHERE challenger_id = '" . (int)$id . "'");
			}
			$this->response->redirect($this->url->link('extension/module/addvacancy/trashChallencher', 'user_token=' . $this->session->data['user_token'], true));
		}
}

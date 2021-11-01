<?php
//microdatapro 7.3

require_once(DIR_SYSTEM . 'library/microdatapro.php');

class ControllerExtensionModuleMicrodataPro extends Controller {

	private $path = 'extension/module/microdatapro';
	private $module = 'marketplace/extension';

	public function __construct($registry) {
		parent::__construct($registry);
		$this->microdatapro = new Microdatapro($this->registry);
	}

	public function install() {
		$response = $this->send();
		if($response['status'] && $response['content']){
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSettingValue('module_microdatapro', "module_microdatapro_license_key", $response['content']);
		}
 		//$this->response->redirect($this->url->link($this->path, 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function index() {$a = 0;

		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$this->transfer(); //transfer data to new version

		$response = $this->send();
		if($response['status'] && $response['content'] && $this->microdatapro->key($response['content'],1)){
			$a = 1;
			$this->model_setting_setting->editSettingValue('module_microdatapro', "module_microdatapro_license_key", $response['content']);
		}

		$data = $this->language->load($this->path);
		$data['href_old'] = $this->path;

		$data['more_info'] = false;
		$data['more_info'] = @file_get_contents('https://microdata.pro/index.php?route=sale/proposal&module=microdatapro');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->model_setting_setting->editSetting('module_microdatapro', $this->request->post);
			if(isset($this->request->get['success'])){ //7.3
				$this->response->redirect($this->url->link($this->path . "&success=1", 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}else{
				$this->response->redirect($this->url->link($this->path, 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
		}

		//7.3
		$data['success'] = false;
		if(isset($this->request->get['success'])){
			$data['success'] = true;
		}

		$heading_title_array = explode(" [", $this->language->get('heading_title'));
		$data['user_token'] = $this->session->data['user_token'];
		$data['heading_title'] = $heading_title_array[0] . ' ' . $this->microdatapro->module_info('version');
		$data['action'] = $this->url->link($this->path . '&success=1', 'user_token=' . $this->session->data['user_token'], 'SSL'); //7.3
		$data['cancel'] = $this->url->link($this->module, 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['site_url'] = str_replace(array("https://", "http://", "/"), "", HTTP_CATALOG);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link($this->module, 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => "MicrodataPRO " . $this->microdatapro->module_info('version'),
			'href' => $this->url->link($this->path, 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['mirodatapro_version'] = $this->microdatapro->module_info('version'); //7.3

		$data['version2'] = false;
		if($this->microdatapro->opencart_version(0) == 2){
			$data['version2'] = true;
		}

		$vars = array(
			'module_microdatapro_license_key',
			'module_microdatapro_status', //3.0
			'module_microdatapro_opengraph',
			'module_microdatapro_opengraph_meta',
			'module_microdatapro_twitter_account',
			'module_microdatapro_company',
			'module_microdatapro_company_type',
			'module_microdatapro_store_type',
			'module_microdatapro_hcard',
			'module_microdatapro_company_syntax',
			'module_microdatapro_email',
			'module_microdatapro_oh_1',
			'module_microdatapro_oh_2',
			'module_microdatapro_oh_3',
			'module_microdatapro_oh_4',
			'module_microdatapro_oh_5',
			'module_microdatapro_oh_6',
			'module_microdatapro_oh_7',
			'module_microdatapro_phones',
			'module_microdatapro_groups',
			'module_microdatapro_locations',
			'module_microdatapro_map',
			'module_microdatapro_product',
			'module_microdatapro_product_syntax',
			'module_microdatapro_product_breadcrumb',
			'module_microdatapro_product_gallery',
			'module_microdatapro_hide_price',
			'module_microdatapro_sku',
			'module_microdatapro_upc',
			'module_microdatapro_ean',
			'module_microdatapro_mpn',
			'module_microdatapro_isbn',
			'module_microdatapro_product_reviews',
			'module_microdatapro_product_related',
			'module_microdatapro_product_attribute',
			'module_microdatapro_product_in_stock',
			'module_microdatapro_in_stock_status_id',
			'module_microdatapro_category',
			'module_microdatapro_category_syntax',
			'module_microdatapro_category_range',
			'module_microdatapro_category_review',
			'module_microdatapro_category_gallery',
			'module_microdatapro_manufacturer',
			'module_microdatapro_manufacturer_syntax',
			'module_microdatapro_information',
			'module_microdatapro_information_syntax',
			'module_microdatapro_age_group',
			'module_microdatapro_target_gender',
			'module_microdatapro_profile_id',
			'module_microdatapro_attr_color',
			'module_microdatapro_attr_material',
			'module_microdatapro_attr_size'
		);

		//add multistore vars
		$store_results = $this->model_setting_store->getStores();
		foreach ($store_results as $result) {
			$vars[] = 'module_microdatapro_phones'.$result['store_id'];
			$vars[] = 'module_microdatapro_groups'.$result['store_id'];
			$vars[] = 'module_microdatapro_locations'.$result['store_id'];
			$vars[] = 'module_microdatapro_map'.$result['store_id'];
		}

 		foreach($vars as $var){
			if (isset($this->request->post[$var])) {
				$data[$var] = $this->request->post[$var];
			} else {
				$data[$var] = $this->config->get($var);
			}
		}

		$data['email'] 			= $this->config->get('config_email');
		$data['store_name'] = $this->config->get('config_name');
		$data['stores'] = array();
		foreach ($store_results as $result) {
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name'],
				'module_microdatapro_phones' => $data['module_microdatapro_phones'.$result['store_id']],
				'module_microdatapro_groups' => $data['module_microdatapro_groups'.$result['store_id']],
				'module_microdatapro_locations' => $data['module_microdatapro_locations'.$result['store_id']],
				'module_microdatapro_map' => $data['module_microdatapro_map'.$result['store_id']]
			);
		}

		$this->load->model('localisation/stock_status');
		$data['stock_statuses'] =  $this->model_localisation_stock_status->getStockStatuses();
		$data['stock_status_id'] = $this->config->get('module_microdatapro_in_stock_status_id');

		$data['old_microdata'] = $this->find_old();

		//7.3

		//OC 3.0
		$data['store_types'] = array();
		for($i=0; $i<30; $i++){
			$data['store_types'][$i] = array(
				'id' => $i + 1,
				'name' => $this->language->get('text_storetype_' . $i)
			);
		}
		//OC 3.0

		$this->load->model('catalog/attribute');
		$data['all_attributes'] = $this->model_catalog_attribute->getAttributes(array('start'=>0,'limit'=>9999,'sort'=>'ad.name','order'=>'ASC'));

		$data['lhref'] = "https://microdata.pro/status/?module=microdatapro&domain=" . $this->microdatapro->module_info('main_host', 1);
		$data['old_count'] = count($data['old_microdata']);
		$data['mod_files'] = $this->mod_files();
		$data['mod_errors'] = $this->mod_files(1);
		$data['other_modules'] = $this->find_other();

		$data['count_errors'] = 0;
		if($data['old_microdata']) $data['count_errors']++;
		if($data['mod_errors']) $data['count_errors']++;
		if($data['other_modules']) $data['count_errors']++;

		$data['link_main'] = HTTPS_CATALOG;
		$data['link_category'] = false;
		$category_query = $this->db->query("SELECT category_id FROM " . DB_PREFIX ."category WHERE status = 1 LIMIT 0,1");
		if($category_query->num_rows){
			$data['link_category'] = HTTPS_CATALOG . 'index.php%3Froute=product/category%26path%3D' . $category_query->row['category_id'];
		}
		$data['link_product'] = false;
		$product_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX ."product WHERE status = 1 LIMIT 0,1");
		if($product_query->num_rows){
			$data['link_product'] = HTTPS_CATALOG . 'index.php%3Froute=product/product%26product_id%3D' . $product_query->row['product_id'];
		}
		$data['link_manufacturer'] = false;
		$manufacturer_query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX ."manufacturer ORDER BY manufacturer_id DESC LIMIT 0,1");
		if($manufacturer_query->num_rows){
			$data['link_manufacturer'] = HTTPS_CATALOG . 'index.php%3Froute=product/manufacturer/info%26manufacturer_id%3D' . $manufacturer_query->row['manufacturer_id'];
		}
		$data['link_information'] = false;
		$information_query = $this->db->query("SELECT information_id FROM " . DB_PREFIX ."information WHERE status = 1 LIMIT 0,1");
		if($information_query->num_rows){
			$data['link_information'] = HTTPS_CATALOG . 'index.php%3Froute=information/information%26information_id%3D' . $information_query->row['information_id'];
		}
		//7.3

		if($response['status'] && $response['content'] && empty($data['module_microdatapro_license_key'])){
			$this->model_setting_setting->editSettingValue('module_microdatapro', "module_microdatapro_license_key", $response['content']);
			$data['module_microdatapro_license_key'] = $response['content'];
		}
		$data['module_microdatapro_license_key'] = $a?$data['module_microdatapro_license_key']:false;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view($this->path, $data));
	}

	//7.3

	public function theme_dir() {
		$theme_dir = $this->config->get('config_theme');
		// if($this->config->get('theme_default_directory')){
		// 	$theme_dir = $this->config->get('theme_default_directory');
		// }
		return $theme_dir;
	}

	public function find_other() {
		$old_microdata = 0;
		foreach($this->microdatapro->getModFiles() as $file => $strings){
			$old_microdata += $this->file_scan($old_microdata, $file);
		}
		foreach($this->microdatapro->getMoreFiles() as $file => $strings){
			$old_microdata += $this->file_scan($old_microdata, $file);
		}

		return $old_microdata;
	}

	public function file_scan($old_microdata, $file) {

		$file_full = DIR_MODIFICATION . str_replace("{theme}", $this->theme_dir(), $file);
		$file_content = "";
		if(is_file($file_full)){
		  $file_content = @file_get_contents($file_full);
		}

		foreach($this->microdatapro->find_old() as $tag){
			$variants = array(
				$tag,
				str_replace("http", "https", $tag),
				str_replace("=", " = ", $tag),
				str_replace('"', "'", $tag),
				str_replace('"', "'", str_replace("http", "https", $tag)),
			);
			foreach($variants as $variant){
				if (stripos($file_content, $variant)){
					$old_microdata++;
				}
			}
		}

		return $old_microdata;
	}

	public function mod_files($key = 0) {
		$mod_files = array();

		$all_modified_files = $this->microdatapro->getModFiles();
		$mod_errors = count($all_modified_files)*2;

		foreach($all_modified_files as $file => $strings){
			$file = str_replace("{theme}", $this->theme_dir(), $file);

			foreach($strings as $string){
				$string = str_replace("&&&", "$", $string);

				$file_full = str_replace("system/", "", DIR_SYSTEM) . str_replace("{theme}", $this->theme_dir(), $file);
				$file_ocmod = DIR_MODIFICATION . str_replace("{theme}", $this->theme_dir(), $file);

				//fix
				if(!is_file($file_full)){
					$file_full = str_replace($this->theme_dir(), 'default', $file_full);
				}
				if(!is_file($file_ocmod)){
					$file_ocmod = str_replace($this->theme_dir(), 'default', $file_ocmod);
				}
				//fix

				if (strpos(file_get_contents($file_full), $string)){ //если есть строка для привязки
					$mod_errors--;
					$mod_files[$file] = array(
						'string' => $string,
						'status' => true,
					);
					$file_ocmod_content = @file_get_contents($file_ocmod);

					if (strpos($file_ocmod_content, $string) && (strpos($file_ocmod_content, "//microdatapro") || strpos($file_ocmod_content, "# microdatapro"))){ //если есть строка и модуль в  модификаторах
						$mod_errors--;
						$mod_files[$file]['ocmod'] = true;
					}else{
						$mod_files[$file]['ocmod'] = false;
					}
					break;
				}else{
					$mod_files[$file] = array(
						'string' => str_replace("&&&", "$", $strings),
						'status' => false,
						'ocmod'  => false
					);
				}

			}

		}

		if($key == 0){
			return $mod_files;
		}
		if($key == 1){
			return $mod_errors;
		}

	}

	public function find_old($original = false) {
		$old_microdata = array();
		$all_variants = array();

		foreach($this->microdatapro->getModFiles() as $file => $string){
			$file_full = str_replace("system/", "", DIR_SYSTEM) . str_replace("{theme}", $this->theme_dir(), $file);
			$file_content = "";
			if(is_file($file_full)){
			  $file_content = @file_get_contents($file_full);
			}
			$file = str_replace("{theme}", $this->theme_dir(), $file);

			foreach($this->microdatapro->find_old() as $tag){
				$variants = array(
					$tag,
					str_replace("http", "https", $tag),
					str_replace("=", " = ", $tag),
					str_replace('"', "'", $tag),
					str_replace('"', "'", str_replace("http", "https", $tag)),
				);
				foreach($variants as $variant){
					$all_variants[] = $variant;
					if (stripos($file_content, $variant)){
						if($original){
							$file = $file_full;
						}
						$old_microdata[$file] = $file;
					}
				}
			}
		}

		foreach($this->microdatapro->getMoreFiles() as $file => $string){
			$file_full = str_replace("system/", "", DIR_SYSTEM) . str_replace("{theme}", $this->theme_dir(), $file);
			$file_content = "";
			if(is_file($file_full)){
			  $file_content = @file_get_contents($file_full);
			}
			$file = str_replace("{theme}", $this->theme_dir(), $file);

			foreach($this->microdatapro->find_old() as $tag){
				$variants = array(
					$tag,
					str_replace("http", "https", $tag),
					str_replace("=", " = ", $tag),
					str_replace('"', "'", $tag),
					str_replace('"', "'", str_replace("http", "https", $tag)),
				);
				foreach($variants as $variant){
					$all_variants[] = $variant;
					if (stripos($file_content, $variant)){
						if($original){
							$file = $file_full;
						}
						$old_microdata[$file] = $file;
					}
				}
			}
		}

		if(!$original){
			return $old_microdata;
		}else{
			return array($old_microdata, $all_variants);
		}
	}

	public function clear_old(){
		$find_files_data = $this->find_old(1);
		$find_files = $find_files_data[0];
		$find_tags = $find_files_data[1];
		if($find_files){
			$this->log->write("============================================================");
			$this->log->write("MicrodataPro " . $this->microdatapro->module_info('version') . " начало очистки шаблона от старых элементов разметки");
			foreach($find_files as $item){
				$file_html = file_get_contents($item);
				$file_html = preg_replace('/<meta property=(|"|\')og:(.*?)\/>/im', "", $file_html); //clear og:
				$file_data = str_ireplace($find_tags, "", $file_html);
				rename($item, $item."_mdb");
				$fp = fopen($item, "w");
				fwrite($fp, $file_data);
				fclose($fp);
				$this->log->write("microdatapro очищенный файл: " . $item);
				$this->log->write("microdatapro оригинальный файл: " . $item . "_mdb");
			}
			$this->log->write("MicrodataPro " . $this->microdatapro->module_info('version') . " завершение чистки шаблона, всего очищено (" . count($find_files) . ") файлов");
			$this->log->write("============================================================");
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(count($find_files)));
	}
	// 7.3

    public function send() {
        $response = array();
        $response['content'] = md5(md5($_SERVER['HTTP_HOST']).'cracked');
        $response['status'] = true;
        return $response;
    }

	public function transfer(){
		if(!$this->config->get('module_microdatapro_new_version')){
			$this->load->model('setting/setting');
			$this->load->model('setting/store');
			$all_vars = array( //new => old
				'module_microdatapro_license_key' => 'config_microdata_license_key',
				'module_microdatapro_status' => 'config_microdata_status',
				'module_microdatapro_opengraph' => 'config_microdata_opengraph',
				'module_microdatapro_opengraph_meta' => 'config_description_meta',
				'module_microdatapro_twitter_account' => 'config_microdata_twitter_account',
				'module_microdatapro_company' => 'config_company',
				'module_microdatapro_hcard' => 'config_hcard',
				'module_microdatapro_company_syntax' => 'config_company_syntax',
				'module_microdatapro_email' => 'config_microdata_email',
				'module_microdatapro_oh_1' => 'config_microdata_oh_1',
				'module_microdatapro_oh_2' => 'config_microdata_oh_2',
				'module_microdatapro_oh_3' => 'config_microdata_oh_3',
				'module_microdatapro_oh_4' => 'config_microdata_oh_4',
				'module_microdatapro_oh_5' => 'config_microdata_oh_5',
				'module_microdatapro_oh_6' => 'config_microdata_oh_6',
				'module_microdatapro_oh_7' => 'config_microdata_oh_7',
				'module_microdatapro_phones' => 'config_microdata_phones',
				'module_microdatapro_groups' => 'config_microdata_groups',
				'module_microdatapro_locations' => 'config_microdata_locations',
				'module_microdatapro_map' => 'config_microdata_map',
				'module_microdatapro_product' => 'config_product_page',
				'module_microdatapro_product_syntax' => 'config_product_syntax',
				'module_microdatapro_product_breadcrumb' => 'config_product_breadcrumb',
				'module_microdatapro_hide_price' => 'config_microdata_hide_price',
				'module_microdatapro_sku' => 'config_microdata_sku',
				'module_microdatapro_upc' => 'config_microdata_upc',
				'module_microdatapro_ean' => 'config_microdata_ean',
				'module_microdatapro_mpn' => 'config_microdata_mpn',
				'module_microdatapro_isbn' => 'config_microdata_isbn',
				'module_microdatapro_product_reviews' => 'config_product_reviews',
				'module_microdatapro_product_related' => 'config_product_related',
				'module_microdatapro_product_attribute' => 'config_product_attribute',
				'module_microdatapro_product_in_stock' => 'config_product_in_stock',
				'module_microdatapro_in_stock_status_id' => 'config_in_stock_status_id',
				'module_microdatapro_category' => 'config_category',
				'module_microdatapro_category_syntax' => 'config_category_syntax',
				'module_microdatapro_manufacturer' => 'config_manufacturer',
				'module_microdatapro_manufacturer_syntax' => 'config_manufacturer_syntax',
				'module_microdatapro_information' => 'config_information_page',
				'module_microdatapro_information_syntax' => 'config_information_syntax'
			);
			//add multistore vars
			$store_results = $this->model_setting_store->getStores();
			foreach ($store_results as $result) {
				$all_vars['module_microdatapro_phones'.$result['store_id']] = 'config_microdata_phones'.$result['store_id'];
				$all_vars['module_microdatapro_groups'.$result['store_id']] = 'config_microdata_groups'.$result['store_id'];
				$all_vars['module_microdatapro_locations'.$result['store_id']] = 'config_microdata_locations'.$result['store_id'];
				$all_vars['module_microdatapro_map'.$result['store_id']] = 'config_microdata_map'.$result['store_id'];
			}

			$key_value = array();
			foreach($all_vars as $new_variable => $old_variable){
				$key_value[$new_variable] = $this->config->get($old_variable);
			}
			$key_value['module_microdatapro_new_version'] = 1;
			$this->model_setting_setting->editSetting('module_microdatapro', $key_value);
			$this->response->redirect($this->url->link($this->path, 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}
	}
}

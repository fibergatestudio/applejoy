<?php
class ControllerInformationTradein extends Controller {
	public function index() {
		// Optional. This calls for your language file
		$this->load->language('information/tradein');

		// Optional. Set the title of your web page
		$this->document->setTitle($this->language->get('heading_title'));

		// Breadcrumbs for the page
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/tradein')
		);

		// Get "heading_title" from language file
		$data['heading_title'] = $this->language->get('heading_title');

		//devices
		$data['devices'] = [];
		$all_devices = $this->getAllDevices();

		foreach ($all_devices->rows as $device){
            if(is_array($device)){
                array_push($data['devices'], $device);
            }
        }
		////////////////////////////////////////////////////////////////////////


		$data['iphone_devices'] = [];
		$iphone_devices = $this->getDeviceByType('iphone');
		foreach ($iphone_devices->rows as $iphone_device){
            if(is_array($iphone_device)){
                array_push($data['iphone_devices'], $iphone_device);
            }
        }

		$data['ipad_devices'] = [];
		$ipad_devices = $this->getDeviceByType('ipad');
		foreach ($ipad_devices->rows as $ipad_device){
            if(is_array($ipad_device)){
                array_push($data['ipad_devices'], $ipad_device);
            }
        }

		$data['mac_devices'] = [];
		$mac_devices = $this->getDeviceByType('mac');
		foreach ($mac_devices->rows as $mac_device){
            if(is_array($mac_device)){
                array_push($data['mac_devices'], $mac_device);
            }
        }

		$data['apple_watch_devices'] = [];
		$apple_watch_devices = $this->getDeviceByType('apple_watch');
		foreach ($apple_watch_devices->rows as $apple_watch_device){
            if(is_array($apple_watch_device)){
                array_push($data['apple_watch_devices'], $apple_watch_device);
            }
        }


		////////////////////////////////////////////////////////////////////////
		$data['prod_devices'] = [];
        $all_prods = $this->getAllProds();

		foreach($all_prods->rows as $prod){
			array_push($data['prod_devices'], $prod);

			$prod_opt = $this->getProdOption($prod["product_id"]);
			//var_dump($prod_opt);

			//$product_id
			//ocjw_product_option
		}
		//var_dump($data["prod_devices"]);
		// echo "<pre>";
		// var_dump($all_prods);
		// echo "</pre>";

		// All the necessary page elements
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		// Load the template file and show output
		$this->response->setOutput($this->load->view('information/tradein', $data));
	}

	public function getDeviceInfoByName(){

		$device_name = $this->request->get["device_name"];

		$query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "tradein_devices" . " WHERE device_name = '". $device_name ."'"
        );

		$this->response->setOutput(json_encode($query));
	}

	//my fun

	public function prodGetDeviceInfoByName(){

		$model = $this->request->get["model"];

		$query = $this->db->query(
						"SELECT * FROM " . DB_PREFIX . "product" . " WHERE model = '". $model ."'"
				);
		foreach($query->rows as $prod) {
			$queryOption = $this->getProdOption($prod["product_id"]);
		}
		$this->response->setOutput(json_encode($queryOption));
	}

	//

	public function checkDevicePrice(){

		$device_name = $this->request->get["device_name"];
		$device_gb = $this->request->get["device_gb"];
		//return "test";
		$query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "tradein_devices" . " WHERE device_name = '". $device_name ."' AND device_gb = '". $device_gb ."'"
        );

		// $this->db->query(
		// 	"UPDATE " .
		// 		DB_PREFIX .
		// 		"category SET image = '' WHERE category_id = '" .
		// 		(int) $category_id .
		// 		"'"
		// );

        //return $query;


		$this->response->setOutput(json_encode($query));
	}

	public function getProdOption($product_id){

		$query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "product_option WHERE product_id = '" .
                (int) $product_id .
                "' AND option_id='14' "
        );

        return $query;
	}

    public function getAllProds(){
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "product"
        );

        return $query;
    }

	public function getDeviceByType($type){
		$query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "tradein_devices WHERE device_type = '".$type."' "
        );

        return $query;
	}

	public function getAllDevices(){
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "tradein_devices"
        );

        return $query;
    }
}

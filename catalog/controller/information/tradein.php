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

			//$prod_opt = $this->getProdOption($prod["product_id"]);
			//var_dump($prod_opt);

			//$product_id
			//ocjw_product_option
		}
		// IPHONEs
		$data['iphones'] = [];
		$macbooks = $this->getProdsByCat("Iphone");
		$arr_true = [];
		foreach($macbooks as $macbook){
			foreach($macbook->rows as $mac){
				//var_dump($mac['product_id']);
				if(!in_array($mac['product_id'], $arr_true)){
					array_push($arr_true, $mac['product_id']);
					array_push($data['iphones'], $mac);
				}
			}
		}
		// IPADs
		$data['ipads'] = [];
		$macbooks = $this->getProdsByCat("Ipad");
		$arr_true = [];
		foreach($macbooks as $macbook){
			foreach($macbook->rows as $mac){
				//var_dump($mac['product_id']);
				if(!in_array($mac['product_id'], $arr_true)){
					array_push($arr_true, $mac['product_id']);
					array_push($data['ipads'], $mac);
				}
			}
		}

		// MACBOOKs
		$data['macbooks'] = [];
		$macbooks = $this->getProdsByCat("MacBook");
		$arr_true = [];
		foreach($macbooks as $macbook){
			foreach($macbook->rows as $mac){
				//var_dump($mac['product_id']);
				if(!in_array($mac['product_id'], $arr_true)){
					array_push($arr_true, $mac['product_id']);
					array_push($data['macbooks'], $mac);
				}
			}
		}

		// APPLEWATCHEs
		$data['applewatches'] = [];
		$macbooks = $this->getProdsByCat("Apple Watch");
		$arr_true = [];
		foreach($macbooks as $macbook){
			foreach($macbook->rows as $mac){
				//var_dump($mac['product_id']);
				if(!in_array($mac['product_id'], $arr_true)){
					array_push($arr_true, $mac['product_id']);
					array_push($data['applewatches'], $mac);
				}
			}
		}

		// BY IPHONEs
		$data['by_iphones'] = [];
		$macbooks = $this->getProdsByCat("Трейдин Б/у iPhone");
		$arr_true = [];
		foreach($macbooks as $macbook){
			foreach($macbook->rows as $mac){
				//var_dump($mac['product_id']);
				if(!in_array($mac['product_id'], $arr_true)){
					array_push($arr_true, $mac['product_id']);
					array_push($data['by_iphones'], $mac);
				}
			}
		}
		// GADGETs
		$data['gadgets'] = [];
		$macbooks = $this->getProdsByCat("Гаджеты");
		$arr_true = [];
		foreach($macbooks as $macbook){
			foreach($macbook->rows as $mac){
				//var_dump($mac['product_id']);
				if(!in_array($mac['product_id'], $arr_true)){
					array_push($arr_true, $mac['product_id']);
					array_push($data['gadgets'], $mac);
				}
			}
		}

		// AIRPODs
		$data['air_pods'] = [];
		$macbooks = $this->getProdsByCat("AirPods");
		$arr_true = [];
		foreach($macbooks as $macbook){
			foreach($macbook->rows as $mac){
				//var_dump($mac['product_id']);
				if(!in_array($mac['product_id'], $arr_true)){
					array_push($arr_true, $mac['product_id']);
					array_push($data['air_pods'], $mac);
				}
			}
		}


		// echo "<pre>";
		// var_dump($data['air_pods']);
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

	public function getDevicePriceProd() {
		$prod_device_id = $this->request->get["prod_device_id"];
		$device_options = $this->request->get["device_options"];

		$w='';
		for ($i=0; $i < count($device_options); $i++) {
		  $w.= $device_options[$i];
		  if($i < count($device_options) -1) $w.= ",";
		}

		$query = $this->db->query(
			"SELECT DISTINCT " .
			DB_PREFIX .
			"product_option_value.price AS option_price," .
			DB_PREFIX .
			"product.price AS model_price," .
			DB_PREFIX .
			"product_option_value.product_option_id  AS id FROM " .
			DB_PREFIX .
			"product LEFT JOIN " .
			DB_PREFIX .
			"product_option_value ON " .
						DB_PREFIX .
						"product.product_id = " .
						DB_PREFIX .
						"product_option_value.product_id WHERE " .
											DB_PREFIX .
											"product_option_value.option_value_id IN (" . $w .") AND " .
																DB_PREFIX .
																"product_option_value.product_id ='". $prod_device_id ."'"
        );

		$this->response->setOutput(json_encode($query));
	}

	public function checkDevicePriceByName(){
		$device_name = $this->request->get["device_name"];

		$query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "tradein_devices" . " WHERE device_name = '". $device_name ."'"
        );

		$this->response->setOutput(json_encode($query));
	}


	public function getProdOption($product_id){

		$query = $this->db->query(
            "SELECT option_value_id FROM " .
                DB_PREFIX .
                "product_option_value WHERE product_id = '" .
                (int) $product_id ."'"
    	);

		$option_value_id = [];
		foreach($query->rows as $prod) {
			foreach($prod as $p) {
				$option_value_id[] = $p;
			}
		}
		$w='';
		for ($i=0; $i < count($option_value_id); $i++) {
		  $w.= $option_value_id[$i];
		  if($i < count($option_value_id) -1) $w.= ",";
		}
		$queryOptions = $this->db->query("SELECT DISTINCT " .
                DB_PREFIX .
                "option_value_description.name AS name_value," .
                DB_PREFIX .
                "option_description.name AS name_option," .
                DB_PREFIX .
                "option_description.option_id AS option_id," .
                DB_PREFIX .
                "option_value_description.option_value_id AS option_value_id FROM " .
                DB_PREFIX .
                "option_value_description LEFT JOIN " .
                DB_PREFIX .
                "option_description ON " .
                                DB_PREFIX .
                                "option_description.option_id = " .
                                DB_PREFIX .
                                "option_value_description.option_id WHERE " .
                                                DB_PREFIX .
                                                "option_value_description.option_value_id IN (" . $w .") AND " .
                                                                DB_PREFIX .
                                                                "option_description.language_id = 1"
																															);
		return $queryOptions;
	}

    public function getAllProds(){
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "product"
        );

        return $query;
    }

	public function getProdsByCat($cat_name){

		//'MacBook'
		//DB_PREFIX . "category"
		$product_array = [];
		$query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "category_description WHERE name = '" . $cat_name . "'");
		foreach($query->rows as $cat){
			//var_dump($cat['category_id']);
			
			$prod_ids_query = $this->db->query( "SELECT * FROM " . DB_PREFIX . "product_to_category WHERE category_id = '" .$cat['category_id'] ."'");

			foreach($prod_ids_query->rows as $prod){

				$prod_to_array = $this->db->query( "SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" .$prod['product_id']."'");
				array_push($product_array, $prod_to_array);
			}
		}


		
		return $product_array;
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

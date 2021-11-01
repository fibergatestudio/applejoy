<?php
//microdatapro 7.5

require_once(DIR_SYSTEM . 'library/microdatapro.php');

class ControllerExtensionModuleMicrodataPro extends Controller {

	public function __construct($registry) {
		parent::__construct($registry);
		$this->microdatapro = new Microdatapro($this->registry);

		if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1' || $_SERVER['HTTPS'])) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'))) {
			$this->host = $this->config->get('config_ssl');
		} else {
			$this->host = $this->config->get('config_url');
		}
	}

	public function index() {
		/************copyright**************/
		/*                                 */
		/*   site:  https://microdata.pro  */
		/*   email: info@microdata.pro     */
		/*                                 */
		/************copyright**************/
	}

	public function company() { //LocalBissness - on all pages
		if($this->config->get('module_microdatapro_status') && $this->config->get('module_microdatapro_company') && $this->microdatapro->key($this->config->get('module_microdatapro_license_key'))){
			$data['company_syntax']     = $this->config->get('module_microdatapro_company_syntax');
			$data['store_type']     		= $this->config->get('module_microdatapro_store_type')?$this->microdatapro->store_type($this->config->get('module_microdatapro_store_type')):'Store';
			$data['config_hcard']	    	= $this->config->get('module_microdatapro_hcard');
			$data['version'] 	        	= $this->microdatapro->module_info('version');
			$data['code']			    			= $this->session->data['currency'];
			$data['organization_name']  = $this->microdatapro->clear($this->config->get('config_name'));
			$data['organization_url']   = $this->host;
			$data['organization_logo']  = str_replace(" ", "%20", $this->host . "image/" . $this->config->get('config_logo'));
			$data['organization_email'] = $this->config->get('module_microdatapro_email')?$this->config->get('module_microdatapro_email'):$this->config->get('config_email');
			$data['organization_phones']= $this->config->get('module_microdatapro_phones')?array_diff(array_map('trim', explode(",", $this->config->get('module_microdatapro_phones'))), array('')):false;
			$data['organization_groups']= $this->config->get('module_microdatapro_groups')?array_diff(array_map('trim', explode(",", $this->config->get('module_microdatapro_groups'))), array('')):false;
			$data['organization_map']		= trim($this->config->get('module_microdatapro_map'));

			$this->load->model('tool/image');
			$data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 144, 144);

			$data['organization_oh'] = array();
			foreach(array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday") as $i => $day){$i++;
				if($this->config->get('module_microdatapro_oh_'.$i)){
					$pre_data = explode("-", $this->config->get('module_microdatapro_oh_'.$i));
					if(isset($pre_data[0]) && isset($pre_data[1])){
						$data['organization_oh'][$day] = array('open' => trim($pre_data[0]),'close' => trim($pre_data[1]));
					}
				}
			}

			$data['organization_locations'] = $this->organization();

			$store_id = false; //multistore
			$query_stores = $this->db->query("SELECT `store_id`, `url` FROM " . DB_PREFIX . "store ORDER BY url");
			if ($query_stores->rows){
				foreach ($query_stores->rows as $result){
					$url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$pos = strpos($url, $result['url']);
					if ($pos !== false) {
						if(!isset($data['organization_phones'])){$data['organization_phones'] = array();}
						if(!isset($data['organization_groups'])){$data['organization_groups'] = array();}
						if(!isset($data['organization_map'])){$data['organization_map'] = array();}
						$store_id = $result['store_id'];
						$data['organization_phones']= $this->config->get('microdatapro_phones'.$store_id)?array_diff(array_map('trim', explode(",", $this->config->get('microdatapro_phones'.$store_id))), array('')):$data['organization_phones'];
						$data['organization_groups']= $this->config->get('microdatapro_groups'.$store_id)?array_diff(array_map('trim', explode(",", $this->config->get('microdatapro_groups'.$store_id))), array('')):$data['organization_groups'];
						$data['organization_map']	= $this->config->get('microdatapro_map'.$store_id)?$this->config->get('microdatapro_map'.$store_id):$data['organization_map'];
						$data['organization_locations'] = $this->organization($store_id);
					}
				}
			}

			//3.0
			$data['organization_locations_json'] = array();
			foreach($data['organization_locations'] as $location_item){
				$data['organization_locations_json'][] = $location_item;
				break;
			}
			$data['count_organization_phones'] = 0;
			$data['count_organization_groups'] = 0;
			$data['count_organization_oh'] = 0;
			if($data['organization_phones']){
				$data['count_organization_phones'] = count($data['organization_phones']);
			}
			if($data['organization_groups']){
				$data['count_organization_groups'] = count($data['organization_groups']);
			}
			if($data['organization_oh']){
				$data['count_organization_oh'] = count($data['organization_oh']);
			}
			//3.0

			return $this->view("company", $data);
		}
	}

	public function category($microdatapro_data) { //Breadcrumbs - on category pages
		if($this->config->get('module_microdatapro_status') && $this->config->get('module_microdatapro_category') && $this->microdatapro->key($this->config->get('module_microdatapro_license_key'))){
			$microdatapro_data = $this->microdatapro->check_variable($microdatapro_data, 'category_manufacturer');
			$data['version'] = $this->microdatapro->module_info('version');
			$data['syntax']  = $this->config->get('module_microdatapro_category_syntax');
			$data['name'] = $this->microdatapro->clear($microdatapro_data['heading_title']);
			$data['image'] = str_replace(" ", "%20", $microdatapro_data['thumb']);
			if(!$microdatapro_data['thumb']){
				$data['image'] = str_replace(" ", "%20", $this->host . 'image/' . $microdatapro_data['microdatapro_data']['image']);
			}
			$data['description'] = $this->microdatapro->clear(isset($microdatapro_data['description'])?$microdatapro_data['description']:'');
			if(!$data['description']){
				$data['description'] = $data['name'];
			}
			$data['images'] = false;
			if($this->config->get('module_microdatapro_category_gallery')){
				$data['author'] = $this->microdatapro->clear($this->config->get('config_name'));

				foreach($microdatapro_data['results'] as $product){
					$data['images'][] = array(
						'name' => $this->microdatapro->clear($product['name']),
						'thumb' => $this->model_tool_image->resize($product['image'], '600', '315'),
						'popup' => str_replace(" ", "%20", $this->host . 'image/' . $product['image']),
						'date_added' => date("Y-m-d", strtotime($product['date_added']))
					);
				}
			}

			$data['range'] = $this->config->get('module_microdatapro_category_range');
			$data['sku'] = '';
			if($data['range']){
				$min_max = array();
				if(count($microdatapro_data['results']) > 1){
					foreach($microdatapro_data['results'] as $product){
						$data['sku'] = $product['model'];
						$product_price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						if($product['special']){
							$product_price = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						}
						$product_price = str_replace(",", ".", $product_price);
						$price = (float)rtrim(preg_replace('/[^\d.]/', '', $product_price), ".");
						if($price){
							$min_max[] = $price;
						}
					}
				}
				if(count($min_max) > 1){
					$data['min'] = (float)rtrim(preg_replace('/[^\d.]/', '', min($min_max)), ".");
					$data['max'] = (float)rtrim(preg_replace('/[^\d.]/', '', max($min_max)), ".");
				}else{
					$data['min'] = $data['max'] = (float)rtrim(preg_replace('/[^\d.]/', '', end($min_max)), ".");
				}
				$data['total'] = count($microdatapro_data['products']);
				$data['code'] = $this->session->data['currency'];
			}

			//new min-max
			$category_id = false;
			if(isset($this->request->get['path'])){
				$parts = explode('_', (string)$this->request->get['path']);
				$category_id = (int)array_pop($parts);
			}
			if($category_id){
				$sql = "SELECT COUNT(*) as count,
								MAX(p.price) as max,
								MIN(p.price) as min,
								GROUP_CONCAT(p.product_id SEPARATOR ',') AS products
								FROM " . DB_PREFIX . "product p
								LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
								LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON(p.product_id = p2c.product_id)
								WHERE p.status = 1
								AND p.price > 0
								AND p.price > 0
								AND p.date_available <= '" . $this->db->escape(date('Y-m-d')) . "'
								AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
								AND p2c.category_id = '" . (int)$category_id . "'";
				$mm_query = $this->db->query($sql);

				if($mm_query->row['min']){
					$data['min'] = round($mm_query->row['min'], 2);
				}
				if($mm_query->row['max']){
					$data['max'] = round($mm_query->row['max'], 2);
				}
				if($mm_query->row['count']){
					$data['total'] = $mm_query->row['count'];
				}

				//special
				if($mm_query->row['products']){
					$sql = "SELECT MAX(price) as smax, MIN(price) as smin
									FROM " . DB_PREFIX . "product_special
									WHERE product_id IN (" . rtrim($mm_query->row['products'], ',') . ")
									AND price > 0
									AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
									AND ((date_start = '0000-00-00' OR date_start < '" . $this->db->escape(date('Y-m-d')) . "')
									AND (date_end = '0000-00-00' OR date_end > '" . $this->db->escape(date('Y-m-d')) . "'))";
					$mms_query = $this->db->query($sql);

					if($mms_query->row['smax'] && $mms_query->row['smax'] < $data['max']){
						$data['max'] = round($mms_query->row['smax'], 2);
					}
					if($mms_query->row['smin'] && $mms_query->row['smin'] < $data['min']){
						$data['min'] = round($mms_query->row['smin'], 2);
					}
				}
				//special
			}
			//new min-max

			$data['review'] = $this->config->get('module_microdatapro_category_review');
			if($data['review']){
				$data['rating_count'] = 0;
				$rating_summ = 0;
				foreach($microdatapro_data['results'] as $product){
					if($product['rating']){
						$data['rating_count']++;
						$rating_summ += $product['rating'];
					}
				}
				if($rating_summ && $data['rating_count']){
					$data['rating_value'] = $rating_summ/$data['rating_count'];
				}
			}

			$data['breadcrumbs'] = $this->breadcrumbs($microdatapro_data['breadcrumbs']);

			//3.0
			$data['count_images'] = 0;
			$data['count_breadcrumbs'] = 0;

			if(is_array($data['images']) && $data['images']){
				$data['count_images'] = count($data['images']);
			}
			if(is_array($data['breadcrumbs']) && $data['breadcrumbs']){
				$data['count_breadcrumbs'] = count($data['breadcrumbs']);
			}
			//3.0

			return $this->view("category_manufacturer", $data);
		}
	}

	public function manufacturer($microdatapro_data) { //Breadcrumbs - on manufacturer pages
		if($this->config->get('module_microdatapro_status') && $this->config->get('module_microdatapro_manufacturer') && $this->microdatapro->key($this->config->get('module_microdatapro_license_key'))){
			$microdatapro_data = $this->microdatapro->check_variable($microdatapro_data, 'category_manufacturer');
			$data['version'] = $this->microdatapro->module_info('version');
			$data['syntax']  = $this->config->get('module_microdatapro_manufacturer_syntax');
			$data['range'] = false;
			$data['review'] = false;
			$data['images'] = false;
			$data['breadcrumbs'] = $this->breadcrumbs($microdatapro_data['breadcrumbs']);
			//3.0
			$data['count_images'] = 0;
			$data['count_breadcrumbs'] = count($data['breadcrumbs']);
			//3.0
			return $this->view("category_manufacturer", $data);
		}
	}

	public function product($microdatapro_data) { //Product - on product page
		if($this->config->get('module_microdatapro_status') && $this->config->get('module_microdatapro_product') && $this->microdatapro->key($this->config->get('module_microdatapro_license_key'))){
			$microdatapro_data = $this->microdatapro->check_variable($microdatapro_data, 'product');
			$url = end($microdatapro_data['breadcrumbs']);
			$data['version']  	 = $this->microdatapro->module_info('version');
			$data['code']	  	   = $this->session->data['currency'];
			$data['syntax']   	 = $this->config->get('module_microdatapro_product_syntax');
			$data['related']  	 = $this->config->get('module_microdatapro_product_related');
			$data['reviews']  	 = $this->config->get('module_microdatapro_product_reviews');
			$data['attribute']	 = $this->config->get('module_microdatapro_product_attribute');
			$data['name'] 		 = $this->microdatapro->clear($microdatapro_data['heading_title']);
			$data['url']  		 = isset($url['href'])?$url['href']:'';
			$data['popup']		 = str_replace(array('id=','"',"'",'mainimage','selector'), "", $microdatapro_data['popup']); //fix zoom module id="mainimage"
			$data['thumb']		 = str_replace(array('id=','"',"'",'mainimage','selector'), "", $microdatapro_data['thumb']); //$microdatapro_data['thumb'];
			$data['manufacturer']= $this->microdatapro->clear($microdatapro_data['manufacturer']);
			$data['model'] = $this->microdatapro->clear($microdatapro_data['model']);
			$data['description'] = trim($this->microdatapro->clear($microdatapro_data['description']));
			$data['author'] = $this->microdatapro->clear($this->config->get('config_name'));
			$data['date_added'] = date("Y-m-d", strtotime($microdatapro_data['microdatapro_data']['date_added']));

			$data['popup'] = str_replace(' ', '%20', $data['popup']);
			$data['thumb'] = str_replace(' ', '%20', $data['thumb']);

			$data['images'] = array();
			if($this->config->get('microdatapro_product_gallery')){
				foreach($microdatapro_data['images'] as $image){
					$data['images'][] = array(
						'thumb' => str_replace(array('id=','"',"'",'mainimage','selector'), "", $image['thumb']),
						'popup' => str_replace(array('id=','"',"'",'mainimage','selector'), "", $image['popup'])
					);
				}
				//$data['images'] = $microdatapro_data['images'];
			}

			$data['price'] = '';

			if(!$this->config->get('module_microdatapro_hide_price')){
				$microdatapro_data['special'] = str_replace(",",".",$microdatapro_data['special']);
				$microdatapro_data['price'] = str_replace(",",".",$microdatapro_data['price']);
				$data['price'] = (float)rtrim(preg_replace('/[^\d.]/', '', $microdatapro_data['special']?$microdatapro_data['special']:$microdatapro_data['price']), ".");

				//option price
				$option_prices = array();
				if(!(int)$data['price'] && $microdatapro_data['options'] && !$this->config->get('microdatapro_hide_price')){
					foreach($microdatapro_data['options'] as $option){
						if(isset($option['product_option_value'])){
							foreach($option['product_option_value'] as $value){
								$option_price = (float)rtrim(preg_replace('/[^\d.]/', '', $value['price']), ".");
								$option_prices[$option_price] = $option_price;
							}
						}
					}
				}
				if($option_prices){$data['price'] = min($option_prices);}

				$roption_prices = array();
				if(isset($microdatapro_data['ro_prices']) && $microdatapro_data['ro_prices']){
					foreach($microdatapro_data['ro_prices'] as $rprice){
						$ro_price = $rprice['special']?$rprice['special']:$rprice['price'];
						$roption_prices[$ro_price] = $ro_price;
					}
					if($option_prices){$data['price'] = min($roption_prices);}
				}
			}

			if($this->config->get('module_microdatapro_product_in_stock')){ //always in stock
				$microdatapro_data['microdatapro_data']['quantity'] = '3274';
			}
			if($this->config->get('module_microdatapro_in_stock_status_id') && !$this->config->get('module_microdatapro_product_in_stock')){
				$stock_status_id_query = $this->db->query("SELECT stock_status_id FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $microdatapro_data['product_id'] . "'");
				if($stock_status_id_query->row['stock_status_id']){
					if($stock_status_id_query->row['stock_status_id'] == $this->config->get('module_microdatapro_in_stock_status_id')){
						$microdatapro_data['microdatapro_data']['quantity'] = '3274';
					}
				}
			}

			$data['stock'] = ($microdatapro_data['microdatapro_data']['quantity'] > 0)?"InStock":"OutOfStock";

			$data['reviews'] 	 = array();
			$data['reviewCount'] = false;
			$data['rating']		 = false;
			if($this->config->get('module_microdatapro_product_reviews')){
				$data['reviewCount'] = ($microdatapro_data['microdatapro_data']['reviews'])?(int)$microdatapro_data['microdatapro_data']['reviews']:false;
				$data['rating']		 = ($microdatapro_data['microdatapro_data']['rating'])?(float)$microdatapro_data['microdatapro_data']['rating']:false;

				$reviews_query = $this->db->query("SELECT author, date_added, rating, text FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$microdatapro_data['product_id'] . "' AND status = '1' ORDER BY date_added DESC");
				if($reviews_query->rows){
					foreach($reviews_query->rows as $key => $review){
						$data['reviews'][count($data['reviews'])+1] = array(
							'author'     => $this->microdatapro->clear($review['author']),
							'date_added' => date("Y-m-d", strtotime($review['date_added'])),
							'rating'     => (int)$review['rating'],
							'text'       => $this->microdatapro->clear($review['text'])
						);
					}
				}
			}

			foreach(array('sku','upc','ean','isbn','mpn') as $item){
				$data[$item] = ($microdatapro_data['microdatapro_data'][$item] && $this->config->get('module_microdatapro_'.$item))?$this->microdatapro->clear($microdatapro_data['microdatapro_data'][$item]):false;
			}

			if(!$data['sku']){$data['sku'] = $data['model'];}
			if(!$data['mpn']){$data['mpn'] = $data['model'];}
			$data['price_valid'] = date('Y-m-d', strtotime('+1 years'));

			$data['attributes'] = array();
			if($this->config->get('module_microdatapro_product_attribute')){
				foreach($microdatapro_data['attribute_groups'] as $attribute_group){
					foreach($attribute_group['attribute'] as $attribute){
						$data['attributes'][count($data['attributes'])+1] = array(
							'text' => $this->microdatapro->clear($attribute['text']),
							'name' => $this->microdatapro->clear($attribute['name'])
						);
					}
				}
			}

			$data['products'] = array();
			if($this->config->get('module_microdatapro_product_related')){
				if(isset($microdatapro_data['products']) && is_array($microdatapro_data['products'])){
					foreach($microdatapro_data['products'] as $related){
						$data['products'][count($data['products'])+1] = array(
							'name' => $this->microdatapro->clear($related['name']),
							'href' => $related['href'],
							'thumb'=> $related['thumb'],
							'price'=> $this->config->get('module_microdatapro_hide_price')?'':(float)rtrim(preg_replace('/[^\d.]/', '', $related['special']?str_replace(",",".",$related['special']):str_replace(",",".",$related['price'])), "."),
						);
					}
				}
			}


			$data['category'] = false;
			$count = count($microdatapro_data['breadcrumbs'])-2;
			if($count && isset($microdatapro_data['breadcrumbs'][$count]['text'])){
				$data['category'] = $this->microdatapro->clear($microdatapro_data['breadcrumbs'][$count]['text']);
			}

			$data['breadcrumbs'] = array();
			if($this->config->get('module_microdatapro_product_breadcrumb')){
				$data['breadcrumbs'] = $this->breadcrumbs($microdatapro_data['breadcrumbs']);
			}

			//3.0
			$data['count_breadcrumbs'] = count($data['breadcrumbs']);
			$data['count_images'] = count($data['images']);
			$data['count_attributes'] = count($data['attributes']);
			$data['count_products'] = count($data['products']);
			$data['count_reviews'] = count($data['reviews']);
			//3.0

			return $this->view("product", $data);
		}
	}

	public function information($information) { //NewsArticle - on information page
		if($this->config->get('module_microdatapro_status') && $this->config->get('module_microdatapro_information') && $this->microdatapro->key($this->config->get('module_microdatapro_license_key'))){
			$information = $this->microdatapro->check_variable($information, 'information');
			$url = end($information['breadcrumbs']);

			if($this->config->get('config_logo') && is_file(DIR_IMAGE . $this->config->get('config_logo'))){
				list($width, $height) = getimagesize(DIR_IMAGE . $this->config->get('config_logo'));
			}else{
				$width = $height = 0;
			}

			$data['version'] 	  = $this->microdatapro->module_info('version');
			$data['syntax']  	  = $this->config->get('module_microdatapro_information_syntax');
			$data['name'] 	 	  = $this->microdatapro->clear($information['heading_title']);
			$data['url']  	 	  = isset($url['href'])?$url['href']:'';
			$data['logo']    	  = str_replace(" ", "%20", $this->host . "image/" . $this->config->get('config_logo'));
			$data['author']  	  = $this->microdatapro->clear($this->config->get('config_name'));
			$data['image_width']  = $width;
			$data['image_height'] = $height;
			$data['date'] 		  = date('Y-m-d', filectime(DIR_SYSTEM . 'library/microdatapro.php'));
			$data['description']  = trim($this->microdatapro->clear($information['description']));
			$data['breadcrumbs'] = $this->breadcrumbs($information['breadcrumbs']);
			$data['organization'] = $this->organization();
			$data['phones'] = $this->config->get('module_microdatapro_phones')?array_diff(array_map('trim', explode(",", $this->config->get('module_microdatapro_phones'))), array('')):false;

			//3.0
			$data['count_breadcrumbs'] = count($data['breadcrumbs']);
			//3.0

			return $this->view("information", $data);
		}
	}

	public function tc_og($tc_og) { //twitter card & open graph - in header of the page
		if(($this->config->get('module_microdatapro_opengraph') || $this->config->get('module_microdatapro_twitter_account')) && $this->config->get('module_microdatapro_status') && $this->microdatapro->key($this->config->get('module_microdatapro_license_key'))){
			$tc_og = $this->microdatapro->check_variable($tc_og, 'tc_og');

			if($this->config->get('module_microdatapro_opengraph_meta') && $tc_og['microdatapro_data']['meta_description']){
				$description = $this->microdatapro->clear($tc_og['microdatapro_data']['meta_description']);
			}elseif($tc_og['description']){
				$description = $this->microdatapro->mbCutString($this->microdatapro->clear($tc_og['description']), 290);
			}else{
				$description = $tc_og['heading_title'];
			}

			$url = end($tc_og['breadcrumbs']);

			$this->load->model('tool/image');
			if($tc_og['microdatapro_data']['image']){
				$data['image'] = str_replace(" ", "%20", $this->host . "image/" . $tc_og['microdatapro_data']['image']);
			}else{
				$data['image'] = str_replace(" ", "%20", $this->host . "image/" . $this->config->get('config_logo'));
			}

			$data['images'] = array();
			if(isset($tc_og['images']) && $tc_og['images']){
				foreach($tc_og['images'] as $image){
					$data['images'][$image['popup']] = $image['popup'];
				}
			}
			$data['images'] = array();

			$data['twitter'] = $this->config->get('module_microdatapro_twitter_account');
			$data['opengraph'] = $this->config->get('module_microdatapro_opengraph');
			$data['twitter_account'] = $this->config->get('module_microdatapro_twitter_account');
			$data['version'] = $this->microdatapro->module_info('version');
			$data['title'] = $this->microdatapro->clear($tc_og['heading_title']);
			$data['description'] = $description;
			$data['url'] = isset($url['href'])?$url['href']:'';
			$data['locale'] = strtolower($this->session->data['language']);
			$data['site_name'] = $this->config->get('config_name');
			$data['og_type'] = "business.business";
			$data['product_page'] = false;
			if(isset($this->request->get['route']) && $this->request->get['route'] == "product/product"){
				$data['og_type'] = "product";
				$data['product_page'] = true;
				$data['product_manufacturer'] = isset($tc_og['manufacturer'])?$tc_og['manufacturer']:'';
				$data['product_category'] = false;
				$count = count($tc_og['breadcrumbs'])-2;
				if($count && isset($tc_og['breadcrumbs'][$count]['text'])){
					$data['product_category'] = $this->microdatapro->clear($tc_og['breadcrumbs'][$count]['text']);
				}
				$data['product_stock'] = ($tc_og['microdatapro_data']['quantity'] > 0)?"instock":"pending";

				$data['ean'] = $tc_og['microdatapro_data']['ean'];
				$data['isbn'] = $tc_og['microdatapro_data']['isbn'];
				$data['upc'] = $tc_og['microdatapro_data']['upc'];

				$data['color'] = false;
				$data['material'] = false;
				$data['size'] = false;

				if($this->config->get('module_microdatapro_attr_color') or $this->config->get('module_microdatapro_attr_material') or $this->config->get('module_microdatapro_attr_size')){ //сделать настройку какие атрибуты цвет и материал
					foreach($tc_og['attribute_groups'] as $group){
						foreach($group['attribute'] as $attribute){
							if($attribute['attribute_id'] == $this->config->get('module_microdatapro_attr_color')){
								$data['color'] = $attribute['text'];
							}
							if($attribute['attribute_id'] == $this->config->get('module_microdatapro_attr_material')){
								$data['material'] = $attribute['text'];
							}
							if($attribute['attribute_id'] == $this->config->get('module_microdatapro_attr_size')){
								$data['size'] = $attribute['text'];
							}
						}
					}
				}
				$data['price'] = false;
				$data['special'] = false;
				$data['currency'] = $this->session->data['currency'];

				if(!$this->config->get('module_microdatapro_hide_price')){
					$tc_og['special'] = str_replace(",",".",$tc_og['special']);
					$tc_og['price'] = str_replace(",",".",$tc_og['price']);
					$data['price'] = (float)rtrim(preg_replace('/[^\d.]/', '', $tc_og['price']), ".");
					$data['special'] = (float)rtrim(preg_replace('/[^\d.]/', '', $tc_og['special']), ".");
				}

				//relateds
				$data['relateds'] = array();
				if(isset($tc_og['products']) && $tc_og['products']){
					foreach($tc_og['products'] as $product){
						$data['relateds'][$product['href']] = $product['href'];
					}
				}

			}

			if(isset($this->request->get['route']) && $this->request->get['route'] == "information/information"){
				$data['og_type'] = "article";
			}

			$data['age_group'] = $this->config->get('module_microdatapro_age_group');
			$data['target_gender'] = $this->config->get('module_microdatapro_target_gender');
			$data['module_microdatapro_profile_id'] = $this->config->get('module_microdatapro_profile_id');

			$data['contacts'] = $this->config->get('module_microdatapro_company');
			if($data['contacts']){

				$store_id = false; //multistore
				$query_stores = $this->db->query("SELECT `store_id`, `url` FROM " . DB_PREFIX . "store ORDER BY url");
				if ($query_stores->rows){
					foreach ($query_stores->rows as $result){
						$url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
						$pos = strpos($url, $result['url']);
						if ($pos !== false) {
							$store_id = $result['store_id'];

							if(!isset($data['organization_phones'])){$data['organization_phones'] = array();}
							if(!isset($data['organization_groups'])){$data['organization_groups'] = array();}
							if(!isset($data['organization_map'])){$data['organization_map'] = array();}

							$data['organization_phones']= $this->config->get('microdatapro_phones'.$store_id)?array_diff(array_map('trim', explode(",", $this->config->get('microdatapro_phones'.$store_id))), array('')):$data['organization_phones'];
							$data['organization_groups']= $this->config->get('microdatapro_groups'.$store_id)?array_diff(array_map('trim', explode(",", $this->config->get('microdatapro_groups'.$store_id))), array('')):$data['organization_groups'];
							$data['organization_map']	= $this->config->get('microdatapro_map'.$store_id)?$this->config->get('microdatapro_map'.$store_id):$data['organization_map'];
							$data['organization_locations'] = $this->organization($store_id);
						}
					}
				}

			  $organizations = $this->organization($store_id);
			  foreach($organizations as $organization){
			    $data['street_address'] = $organization['streetAddress'];
			    $data['postal_code'] = $organization['postalCode'];
			    $country_data = explode(",", $organization['addressLocality']);
			    $data['country_name'] = isset($country_data[1])?trim($country_data[1]):'';
			    $data['locality'] = isset($country_data[0])?trim($country_data[0]):'';
			    $data['latitude'] = $organization['latitude'];
			    $data['longitude'] = $organization['longitude'];
			    break;
			  }
			  $data['email'] = $this->config->get('module_microdatapro_email')?$this->config->get('module_microdatapro_email'):$this->config->get('config_email');
			  $data['telephone'] = false;
			  if($this->config->get('module_microdatapro_phones')){
			    $phones_data = $this->config->get('module_microdatapro_phones')?array_diff(array_map('trim', explode(",", $this->config->get('module_microdatapro_phones'))), array('')):false;
			    foreach($phones_data as $phone){
			      $data['telephone'] = $phone;
			      break;
			    }
			  }
			}

			return $this->view("tc_og", $data);
		}
	}

	public function tc_og_prefix() { //twitter card & open graph - in header of the page
		$prefix = 'prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# business: http://ogp.me/ns/business#"';

		if(isset($this->request->get['route'])){
			$route = $this->request->get['route'];

			if($route == "product/product"){
				$prefix = 'prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# product: http://ogp.me/ns/product#"';
			}

		}

		return $prefix;
	}

	public function view($template, $data) {
		return $this->load->view('extension/module/microdatapro/' . $template, $data);
	}

	public function breadcrumbs($breadcrumb_data = array()){
	  $breadcrumbs = array();
	  foreach($breadcrumb_data as $breadcrumb){
	    if(isset($breadcrumb['href'])){
	      $breadcrumb_text = $this->microdatapro->clear($breadcrumb['text']);
	  		if($breadcrumb_text == "" || $breadcrumb_text == " " || $breadcrumb_text == "  " || $breadcrumb_text == "   "){
	  			$breadcrumb_text = "Main";
	  			if(isset($this->session->data['language'])){
	  				if($this->session->data['language'] == "ru" || $this->session->data['language'] == "ru-ru" || $this->session->data['language'] == "russian"){
	  					$breadcrumb_text = "Главная";
	  				}

						if($this->session->data['language'] == "ua" || $this->session->data['language'] == "uk-ua" || $this->session->data['language'] == "ukrainian"){
							$breadcrumb_text = "Головна";
						}

	  			}
	  		}
	      $breadcrumbs[count($breadcrumbs)+1] = array(
	        'text' => $breadcrumb_text,
	        'href' => $breadcrumb['href'],
	      );
	    }
	  }
	  return $breadcrumbs;
	}

	public function organization($store_id = ''){
	  $location_data = array();
	  if($this->config->get('module_microdatapro_locations'.$store_id)){
	    $microdatapro_locations = explode(PHP_EOL, $this->config->get('module_microdatapro_locations'.$store_id));
	    foreach($microdatapro_locations as $location){
	      $geo = explode("//", trim($location));
	      if($geo[0]){
					$geo[0] = str_replace(",", ";", $geo[0]);
	        $coordinates = explode(";", $geo[0]);
	        if(isset($coordinates[0]) && isset($coordinates[1]) && isset($geo[1]) && isset($geo[2]) && isset($geo[3])){
	          $location_data[] = array(
	            'latitude'  	  => trim($coordinates[0]),
	            'longitude' 	  => trim($coordinates[1]),
	            'addressLocality' => trim($geo[1]),
	            'streetAddress'   => trim($geo[2]),
	            'postalCode' 	  => trim($geo[3])
	          );
	        }
	      }
	    }
	  }

	  return $location_data;
	}

}

<?php
class ModelRepairRepair extends Model {

	public function getCategoriesMock() {
        $lang = $this->language->get('code');

        $image_path = "catalog/view/theme/applejoy/template/html/images/";

        $data = [
            1 => [
                'category_id' => 1,
                'name' => 'iPhone',
                'image' => $image_path.'ihpone-category-repair.71245639.webp',
                'models'=>[
                    0=>[ 'product_id' => 1, 'image' => $image_path."iphone-12-pro-max.846676b7.webp", 'name' =>"iPhone 12 Pro Max"],
                    1=>[ 'product_id' => 2, 'image' => $image_path."iphone-12-pro.c67f4c89.webp", 'name' =>"iPhone 12 Pro"],
                    2=>[ 'product_id' => 3, 'image' => $image_path."iphone-12.7f35d9ea.webp", 'name' =>"iPhone 12"],
                    3=>[ 'product_id' => 4, 'image' => $image_path."iphone-12-mini.57ff133b.webp", 'name' =>"iPhone 12 Mini"],
                    4=>[ 'product_id' => 5, 'image' => $image_path."iphone-11-pro-max.27199f7d.webp", 'name' =>"iPhone 11 Pro Max"],
                    5=>[ 'product_id' => 6, 'image' => $image_path."iphone-11-pro.ac87927e.webp", 'name' =>"iPhone 11 Pro"],
                    6=>[ 'product_id' => 7, 'image' => $image_path."iphone-11.0abfd7cf.webp", 'name' =>"iPhone 11"],
                    7=>[ 'product_id' => 8, 'image' => $image_path."iphone-se.db8c3c6b.webp", 'name' =>"iPhone SE"],
                    8=>[ 'product_id' => 9, 'image' => $image_path."iphone-xs-max.a55ee890.webp", 'name' =>"iPhone Xs Max"],
                    9=>[ 'product_id' => 10, 'image' => $image_path."iphone-xr.3b8f40ee.webp", 'name' =>"iPhone Xr"],
                    10=>[ 'product_id' => 11, 'image' => $image_path."iphone-xs.a55ee890.webp", 'name' =>"iPhone Xs"],
                    11=>[ 'product_id' => 12, 'image' => $image_path."iphone-8.d0071537.webp", 'name' =>"iPhone 8"],
                    12=>[ 'product_id' => 13, 'image' => $image_path."iphone-8-plus.9aa3a3b9.webp", 'name' =>"iPhone 8 Plus"],
                    13=>[ 'product_id' => 14, 'image' => $image_path."iphone-7.55abf3de.webp", 'name' =>"iPhone 7"],
                    14=>[ 'product_id' => 15, 'image' => $image_path."iphone-7-plus.b395a072.webp", 'name' =>"iPhone 7 Plus"],
                ]
            ],
            2 => [
                'category_id' => 2,
                'name' => 'iPad',
                'image' => $image_path.'ipad-category-repair.bd420e52.webp',
                'models'=>[
                    0=>['product_id' => 22,'image'=>$image_path."ipad-air-10-9.dc018b55.webp", 'name'=>"iPad Air 10.9''"],
                    1=>['product_id' => 23,'image'=>$image_path."ipad-air-10-5.d5108cf0.webp", 'name'=>"iPad Air 10.5''"],
                    2=>['product_id' => 24,'image'=>$image_path."ipad-pro-12-9.0fe3e825.webp", 'name'=>"iPad Pro 12.9''"],
                    3=>['product_id' => 25,'image'=>$image_path."ipad-pro-11.21e8859b.webp", 'name'=>"iPad Pro 11''"],
                    4=>['product_id' => 26,'image'=>$image_path."ipad-mini.67bf0c58.webp", 'name'=>"iPad Mini 5''"],     
                ]
            ],
            3 => [
                'category_id' => 3,
                'name' => 'Apple Watch',
                'image' => $image_path.'watch-category-repair.e7052bde.webp',
                'models'=>[
                    0=>['product_id' => 31,'image'=>$image_path."series-se.ca95f1c1.webp", 'name'=>"Series SE"],
                    1=>['product_id' => 32,'image'=>$image_path."series-6.f84b4e27.webp", 'name'=>"Series 6"],
                    2=>['product_id' => 33,'image'=>$image_path."series-5.1a05bf0d.webp", 'name'=>"Series 5"],
                    3=>['product_id' => 34,'image'=>$image_path."series-4.1beeecd9.webp", 'name'=>"Series 4"],
                    4=>['product_id' => 35,'image'=>$image_path."series-3.d82b73ab.webp", 'name'=>"Series 3"],

                ]
            ],
            4 => [
                'category_id' => 4,
                'name' => 'Mac',
                'image' => $image_path.'macbook-category-repair.65e6f7a3.webp',
                'models'=>[
                    0=>['product_id' => 41,'image'=>$image_path."macbook-pro-16.429b9308.webp", 'name'=>"Macbook Pro 16\""],
                    1=>['product_id' => 42,'image'=>$image_path."macbook-pro-15.bacfe3a1.webp", 'name'=>"MacBook Pro 15\""],
                    2=>['product_id' => 43,'image'=>$image_path."macbook-pro-13.f9f7def1.webp", 'name'=>"MacBook Pro 13\""],
                    3=>['product_id' => 44,'image'=>$image_path."macbook-air-13.fab9bf5e.webp", 'name'=>"MacBook Air 13\""],
                    4=>['product_id' => 45,'image'=>$image_path."macbook-pro-12.8eeb2473.webp", 'name'=>"MacBook 12\""],
                ]
            ],
        ];
        if($lang == 'ua'){
            $data[1]['models'][] = [ 'product_id' => 16, 'image' => $image_path."iphone-other.c01e15f1.webp", 'name' =>"Минулі моделі"];
            $data[1]['models'][0]['repair'] = [
                'top' => [
                    'left_col' => [
                        0=>['name' => 'Діагностика','price'=>'Безкоштовно'],
                        1=>['name' => 'Відновлення після води','price'=>'Від 499'],
                        2=>['name' => 'Ремонт Face ID','price'=>'Уточнюйте'],
                        3=>['name' => 'Заміна вібромотора','price'=>'1299'],
                        4=>['name' => 'Відновлення герметичності','price'=>'999'],
                        5=>['name' => 'Відновлення imei','price'=>'Уточнюйте'],
                        6=>['name' => 'Ремонт NFC','price'=>'Уточнюйте'],
                        7=>['name' => 'Заміна тачконтроллера тачскрін','price'=>'Уточнюйте'],
                        8=>['name' => 'Заміна мікросхеми (USB фільтр, U2, Трістар)','price'=>'Уточнюйте'],
                    ],
                    'right_col' => [
                        0=>['name' => 'Заміна компаса, гіроскопа','price'=>'Уточнюйте'],
                        1=>['name' => 'Пересадка пар','price'=>'Уточнюйте'],
                        2=>['name' => 'Заміна флеш пам’яті','price'=>'Уточнюйте'],
                        3=>['name' => 'Відновлення підсвічування на материнській платі','price'=>'Уточнюйте'],
                        4=>['name' => 'Обмін по гарантії','price'=>'Уточнюйте'],
                        5=>['name' => 'Ремонт помилки 9','price'=>'Уточнюйте'],
                        6=>['name' => 'Зняття iCloud','price'=>'Уточнюйте'],
                    ]
                ],
                'bottom' => [
                    0=>[    
                        'name' => 'Екран / Дисплей',
                        'issues' => [
                            0=>['name' => 'Заміна дисплея','price'=>'Уточнюйте'],
                            1=>['name' => 'Заміна скла дисплея','price'=>'Уточнюйте'],
                            2=>['name' => 'Заміна скла із сенсором','price'=>'Уточнюйте'],
                            3=>['name' => 'Заміна рамки дисплея','price'=>'Уточнюйте'],
                        ]
                    ],
                    1=>[    
                        'name' => 'Батарея / Зарядка',
                        'issues' => [
                            0=>['name' => 'Заміна акумулятора','price'=>'2299'],
                            1=>['name' => 'Заміна порта зарядки','price'=>'1199'],
                            2=>['name' => 'Ремонт бездротової зарядки','price'=>'Уточнюйте'],
                            3=>['name' => 'Заміна контролера живлення','price'=>'Уточнюйте'],
                            4=>['name' => 'Заміна коннектора акумулятора','price'=>'Уточнюйте'],
                        ],
                    ],
                    2=>[    
                        'name' => 'Звук / Динаміки',
                        'issues' => [
                            0=>['name' => 'Заміна слухового динаміка','price'=>'1199'],
                            1=>['name' => 'Заміна поліфонічного динаміка','price'=>'1249'],
                            2=>['name' => 'Заміна аудіо кодека','price'=>'Уточнюйте'],
                        ],
                    ],
                    3=>[    
                        'name' => 'Зв\'язок / Мережа',
                        'issues' => [
                            0=>['name' => 'Заміна антенн GSM','price'=>'від 1049'],
                            1=>['name' => 'Заміна Wi-Fi модуля','price'=>'Уточнюйте'],
                        ],
                    ],
                    4=>[    
                        'name' => 'Камери',
                        'issues' => [
                            0=>['name' => 'Заміна передньої камери','price'=>'1799'],
                            1=>['name' => 'Заміна задньої камери','price'=>'2849'],
                            2=>['name' => 'Заміна скла камери','price'=>'(1шт.) 1399'],
                            3=>['name' => 'Ремонт спалаху','price'=>'Уточнюйте'],
                            4=>['name' => 'Заміна дисплея','price'=>'Уточнюйте'],
                        ],
                    ],
                    5=>[    
                        'name' => 'Кнопки',
                        'issues' => [
                            0=>['name' => 'Заміна кнопки гучності','price'=>'1149'],
                            1=>['name' => 'Заміна кнопки включення','price'=>'1149'],
                            2=>['name' => 'Заміна перемикача режимів','price'=>'1349'],
                        ],
                    ],
                    6=>[    
                        'name' => 'Корпус',
                        'issues' => [
                            0=>['name' => 'Заміна кришки / корпусу','price'=>'Уточнюйте'],
                            1=>['name' => 'Заміна заднього скла','price'=>'2849'],
                        ],
                    ],
                    7=>[    
                        'name' => 'Розмови',
                        'issues' => [
                            0=>['name' => 'Заміна нижнього мікрофона','price'=>'1349'],
                        ],
                    ],
                ]
            ];
            $data[99] = [
                'category_id' => 99,
                'modal' => true,
                'name' => 'Інший гаджет',
                'image' => $image_path.'other-category-repair.8ff9254e.webp',
            ];
        }
        else {
            $data[1]['models'][] = [ 'product_id' => 16, 'image' => $image_path."iphone-other.c01e15f1.webp", 'name' =>"Другие модели"];
            $data[99] = [
                'category_id' => 99,
                'modal' => true,
                'name' => 'Другой гаджет',
                'image' => $image_path.'other-category-repair.8ff9254e.webp',
            ];
        }
		return $data;
	}

    public function getProductByID($id){
        $category_data = $this->getCategoriesMock();
		foreach ($category_data as $key => $category) {
            $models = $category['models'];
            unset($category['models']);
			foreach ($models as $key => $product) {
				if($product['product_id'] == $this->request->get['id']){
                    $category['model'] = $product;
					return $category;
				}
			}
		}
	}
    

}

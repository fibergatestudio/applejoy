<?php

class ControllerCatalogTradein extends Controller
{
    private $error = [];

    public function index()
    {
        $this->load->language("catalog/tradein");

        $this->document->setTitle($this->language->get("heading_title"));

        $this->load->model("catalog/tradein");

        $this->getList();
    }

    public function getList()
    {
        if (isset($this->request->get["sort"])) {
            $sort = $this->request->get["sort"];
        } else {
            $sort = "name";
        }

        if (isset($this->request->get["order"])) {
            $order = $this->request->get["order"];
        } else {
            $order = "ASC";
        }

        if (isset($this->request->get["page"])) {
            $page = (int) $this->request->get["page"];
        } else {
            $page = 1;
        }

        $url = "";

        if (isset($this->request->get["sort"])) {
            $url .= "&sort=" . $this->request->get["sort"];
        }

        if (isset($this->request->get["order"])) {
            $url .= "&order=" . $this->request->get["order"];
        }

        if (isset($this->request->get["page"])) {
            $url .= "&page=" . $this->request->get["page"];
        }

        $data["breadcrumbs"] = [];

        $data["breadcrumbs"][] = [
            "text" => $this->language->get("text_home"),

            "href" => $this->url->link(
                "common/dashboard",
                "user_token=" . $this->session->data["user_token"],
                true
            ),
        ];

        $data["breadcrumbs"][] = [
            "text" => $this->language->get("heading_title"),

            "href" => $this->url->link(
                "catalog/tradein",
                "user_token=" . $this->session->data["user_token"] . $url,
                true
            ),
        ];

        $data["add"] = $this->url->link(
            "catalog/tradein/add",
            "user_token=" . $this->session->data["user_token"] . $url,
            true
        );

        $data["deleteDevice"] = $this->url->link(
            "catalog/tradein/deleteDevice",
            "user_token=" . $this->session->data["user_token"] . $url,
            true
        );

        // Form action Iphone
        $data["add_iphone"] = $this->url->link(
            "catalog/tradein/addIphoneDevice",
            "user_token=" . $this->session->data["user_token"] . $url,
            true
        );
        // Form action Ipad
        $data["add_ipad"] = $this->url->link(
            "catalog/tradein/addIpadDevice",
            "user_token=" . $this->session->data["user_token"] . $url,
            true
        );
        // Form action Mac
        $data["add_mac"] = $this->url->link(
            "catalog/tradein/addMacDevice",
            "user_token=" . $this->session->data["user_token"] . $url,
            true
        );
        // Form action Apple Watch
        $data["add_apple_watch"] = $this->url->link(
            "catalog/tradein/addAppleWatchDevice",
            "user_token=" . $this->session->data["user_token"] . $url,
            true
        );

        $data["repair"] = $this->url->link(
            "catalog/tradein/repair",
            "user_token=" . $this->session->data["user_token"] . $url,
            true
        );

        $data["categories"] = [];

        $filter_data = [
            "sort" => $sort,

            "order" => $order,

            "start" => ($page - 1) * $this->config->get("config_limit_admin"),

            "limit" => $this->config->get("config_limit_admin"),
        ];

        // Товары на сайте
        $data["prod_devices"] = [];
        $all_prods = $this->getAllProds();
        $data["prod_devices"] = $all_prods;
        //var_dump()
        

        // Все девайсы
        $data["devices"] = [];
        $all_devices = $this->getAllDevices();

        //$data['devices'] = $all_devices;
        //var_dump($all_devices);

        //$category_total = $this->model_catalog_tadein->getTotalCategories();

        // $results = $this->model_catalog_tadein->getCategories($filter_data);
        // echo "<pre>";
        // var_dump($all_devices);
        // echo "</pre>";

        foreach ($all_devices->rows as $device){
            //var_dump($device);
            if(is_array($device)){

                //echo "<pre>";
                //var_dump($device);
                //echo "</pre>";
                array_push($data["devices"], $device);
            }
            
        }
        //var_dump($data["devices"]);

        foreach ($results as $result) {
            $data["categories"][] = [
                "category_id" => $result["category_id"],

                "name" => $result["name"],

                "sort_order" => $result["sort_order"],

                "edit" => $this->url->link(
                    "catalog/tradein/edit",
                    "user_token=" .
                        $this->session->data["user_token"] .
                        "&category_id=" .
                        $result["category_id"] .
                        $url,
                    true
                ),

                "delete" => $this->url->link(
                    "catalog/tradein/delete",
                    "user_token=" .
                        $this->session->data["user_token"] .
                        "&category_id=" .
                        $result["category_id"] .
                        $url,
                    true
                ),
            ];
        }

        if (isset($this->error["warning"])) {
            $data["error_warning"] = $this->error["warning"];
        } else {
            $data["error_warning"] = "";
        }

        if (isset($this->session->data["success"])) {
            $data["success"] = $this->session->data["success"];

            unset($this->session->data["success"]);
        } else {
            $data["success"] = "";
        }

        if (isset($this->request->post["selected"])) {
            $data["selected"] = (array) $this->request->post["selected"];
        } else {
            $data["selected"] = [];
        }

        $url = "";

        if ($order == "ASC") {
            $url .= "&order=DESC";
        } else {
            $url .= "&order=ASC";
        }

        if (isset($this->request->get["page"])) {
            $url .= "&page=" . $this->request->get["page"];
        }

        $data["sort_name"] = $this->url->link(
            "catalog/tradein",
            "user_token=" .
                $this->session->data["user_token"] .
                "&sort=name" .
                $url,
            true
        );

        $data["sort_sort_order"] = $this->url->link(
            "catalog/tradein",
            "user_token=" .
                $this->session->data["user_token"] .
                "&sort=sort_order" .
                $url,
            true
        );

        $url = "";

        if (isset($this->request->get["sort"])) {
            $url .= "&sort=" . $this->request->get["sort"];
        }

        if (isset($this->request->get["order"])) {
            $url .= "&order=" . $this->request->get["order"];
        }

        // $pagination = new Pagination();

        // $pagination->total = $category_total;

        // $pagination->page = $page;

        // $pagination->limit = $this->config->get("config_limit_admin");

        // $pagination->url = $this->url->link(
        //     "catalog/tradein",
        //     "user_token=" .
        //         $this->session->data["user_token"] .
        //         $url .
        //         "&page={page}",
        //     true
        // );

        // $data["pagination"] = $pagination->render();

        // $data["results"] = sprintf(
        //     $this->language->get("text_pagination"),
        //     $category_total
        //         ? ($page - 1) * $this->config->get("config_limit_admin") + 1
        //         : 0,
        //     ($page - 1) * $this->config->get("config_limit_admin") >
        //     $category_total - $this->config->get("config_limit_admin")
        //         ? $category_total
        //         : ($page - 1) * $this->config->get("config_limit_admin") +
        //             $this->config->get("config_limit_admin"),
        //     $category_total,
        //     ceil($category_total / $this->config->get("config_limit_admin"))
        // );

        $data["sort"] = $sort;

        $data["order"] = $order;

        $data["header"] = $this->load->controller("common/header");

        $data["column_left"] = $this->load->controller("common/column_left");

        $data["footer"] = $this->load->controller("common/footer");

        $this->response->setOutput(
            $this->load->view("catalog/tradein_list", $data)
        );
    }


    public function getAllProds(){
        $query = $this->db->query(
            "SELECT * FROM " .
                DB_PREFIX .
                "product"
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

    public function deleteDevice(){

        $delete_device_id = $this->request->post["device_id"];

        $this->db->query(
            "DELETE FROM " .
                DB_PREFIX .
                "tradein_devices WHERE tradein_id = '" .
                (int) $delete_device_id .
                "'"
        );

        $this->getList();
    }

    public function addIphoneDevice(){
        $device_type = $this->request->post["device_type"];
        $device_name = $this->request->post["device_name"];
        $device_gb = $this->request->post["device_gb"];
        //$device_conn = $this->request->post["device_conn"];
        //$device_year = $this->request->post["device_year"];
        $device_discount = $this->request->post["device_discount"];
        //$data = [ "device_type" => "test", "device_name" => "test1", "device_gb" => "test3", "device_conn" => "test4",	"device_year" => "2222", "device_discount" => "322" ];
        $this->db->query(
            "INSERT INTO " .
                DB_PREFIX .
                "tradein_devices SET device_type = '".$device_type."', `device_name` = '".$device_name."', `device_gb` = '".$device_gb."', device_discount = '".$device_discount."'"
        );
        $data = [];
        $this->getList();
    }

    public function addIpadDevice(){
        $device_type = $this->request->post["device_type"];
        $device_name = $this->request->post["device_name"];
        $device_gb = $this->request->post["device_gb"];
        $device_conn = $this->request->post["device_conn"];
        //$device_year = $this->request->post["device_year"];
        $device_discount = $this->request->post["device_discount"];
        //$data = [ "device_type" => "test", "device_name" => "test1", "device_gb" => "test3", "device_conn" => "test4",	"device_year" => "2222", "device_discount" => "322" ];
        $this->db->query(
            "INSERT INTO " .
                DB_PREFIX .
                "tradein_devices SET device_type = '".$device_type."', `device_name` = '".$device_name."', `device_gb` = '".$device_gb."', `device_conn` = '".$device_conn."', device_discount = '".$device_discount."'"
        );
        $data = [];
        $this->getList();

    }
    public function addMacDevice(){
        $device_type = $this->request->post["device_type"];
        $device_name = $this->request->post["device_name"];
        $device_gb = $this->request->post["device_gb"];
        //$device_conn = $this->request->post["device_conn"];
        $device_year = $this->request->post["device_year"];
        $device_discount = $this->request->post["device_discount"];
        //$data = [ "device_type" => "test", "device_name" => "test1", "device_gb" => "test3", "device_conn" => "test4",	"device_year" => "2222", "device_discount" => "322" ];
        $this->db->query(
            "INSERT INTO " .
                DB_PREFIX .
                "tradein_devices SET device_type = '".$device_type."', `device_name` = '".$device_name."', `device_gb` = '".$device_gb."', `device_year` = '".$device_year."', device_discount = '".$device_discount."'"
        );
        $data = [];
        $this->getList();
    }
    public function addAppleWatchDevice(){
        $device_type = $this->request->post["device_type"];
        $device_name = $this->request->post["device_name"];
        //$device_gb = $this->request->post["device_gb"];
        //$device_conn = $this->request->post["device_conn"];
        //$device_year = $this->request->post["device_year"];
        //$device_discount = $this->request->post["device_discount"];
        //$data = [ "device_type" => "test", "device_name" => "test1", "device_gb" => "test3", "device_conn" => "test4",	"device_year" => "2222", "device_discount" => "322" ];
        $this->db->query(
            "INSERT INTO " .
                DB_PREFIX .
                "tradein_devices SET device_type = '".$device_type."', `device_name` = '".$device_name."'"
        );
        $data = [];
        $this->getList();
    }
}

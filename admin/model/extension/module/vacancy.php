<?php
class ModelExtensionModuleVacancy extends Model {

  public function savePost($data, $data_translate){

        $this->db->query("INSERT INTO " . DB_PREFIX . "vacancies SET title = '" . $this->db->escape($data['title']) .
         "', `short_description` = '" . $this->db->escape($data['short_description']) . "', `price` = '" . $this->db->escape($data['price']) .
         "', `description` = '" .$this->db->escape($data['description']). "', vacancy_image = '" . $data['vacancy_image'] .
         "', meta_title = '" . $this->db->escape($data['meta_title']) ."', slug = '".$data['slug'].
         "', meta_description = '" . $this->db->escape($data['meta_description']) .  "', meta_keywords = '" . $this->db->escape($data['meta_keywords']) .
         "', status = '" . $data['status'] . "', modified_at = NOW(), created_at = NOW()");

    		$last_id = $this->db->getlastid();

    		if (isset($data['slug'])){

    			$store_id= 0;

    			$language_id = 1;

    			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'vacancy_id=" . (int)$last_id . "', keyword = '" . $this->db->escape($data['slug']) . "'");
    		}
        foreach ($data_translate as $lang => $value) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "vacancy_translate SET vacancy_id = '" . $last_id . "', language_id = '" . (int)$lang . "', vacancy_title='" . $this->db->escape($value["vacancy_title"]) . "', vacancy_description = '" . $this->db->escape($value['vacancy_description']) . "', vacancy_short_description = '".$this->db->escape($value["vacancy_short_desc"])."'");
        }

    		return $last_id;
  }

  public function getVacanciesCount(){
    $result = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "vacancies WHERE `status` != 'Trash'");
    return (int)$result->rows[0]["COUNT(*)"];
  }

  public function getVacancies($offs, $lim){
    $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancies WHERE `status` != 'Trash' ORDER BY `created_at` DESC LIMIT ". $lim . " OFFSET ". $offs);
    return $result->rows;
  }

  public function getVacancy($id){
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancies WHERE `vacancy_id` = ". $id);

    return $query->rows;
  }

  public function updateVacancy($data,$id){
    $this->db->query("UPDATE " . DB_PREFIX . "vacancies SET title = '" . $this->db->escape($data['title']) .
     "', `short_description` = '" . $this->db->escape($data['short_description']) . "', `price` = '" . $this->db->escape($data['price']) .
     "', `description` = '" .$this->db->escape($data['description']). "', vacancy_image = '" . $data['vacancy_image'] .  "', meta_title = '" . $this->db->escape($data['meta_title']) ."', slug = '".$data['slug'].
     "', meta_description = '" . $this->db->escape($data['meta_description']) .  "', meta_keywords = '" . $this->db->escape($data['meta_keywords']) .
     "', status = '" . $data['status'] . "', modified_at = NOW() WHERE `vacancy_id` = ". $id);
     $test_update = $this->getVacancy($id);
  }

  public function updateTranslate($arr_translate){
    foreach($arr_translate as $item_id => $value){
      if((int)$item_id != 0){
        $this->db->query("UPDATE " . DB_PREFIX . "vacancy_translate SET vacancy_title='" . $this->db->escape($value["vacancy_title"]) . "', vacancy_description = '" . $this->db->escape($value['vacancy_description']) . "', vacancy_short_description = '".
        $this->db->escape($value["vacancy_short_description"])."' WHERE vacancy_id = '" . $value["vacancy_id"] . "' AND language_id = '" . $value["language_id"]."'");
      } else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "vacancy_translate SET vacancy_id = '" . $value["vacancy_id"] . "', language_id = '" . $value["language_id"] . "', vacancy_title='" . $this->db->escape($value["vacancy_title"]) . "', vacancy_description = '" . $this->db->escape($value['vacancy_description']) . "', vacancy_short_description = '".
        $this->db->escape($value["vacancy_short_description"])."'");
      }
    }
    return true;
  }

  public function addVacancyTranslate($add_arr,$id){
    foreach ($add_arr as $item_id => $value) {
      $this->db->query("INSERT INTO " . DB_PREFIX . "vacancy_translate SET vacancy_id = '" . $value["vacancy_id"] . "', language_id = '" . $value["language_id"] . "', vacancy_title='" . $this->db->escape($value["vacancy_title"]) . "', vacancy_description = '" . $this->db->escape($value['vacancy_description']) . "', vacancy_short_description = '".
      $this->db->escape($value["vacancy_short_description"])."'");
    }
  }

  public function getTranslate($id){
    $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancy_translate WHERE vacancy_id = ". $id);
    return $result->rows;
  }

  public function getTrashVacanciesCount(){
    $result = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "vacancies WHERE `status` = 'Trash'");
    return (int)$result->rows[0]["COUNT(*)"];
  }

  public function getTrashVacancies($offs, $lim){
    $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancies WHERE `status` = 'Trash' ORDER BY `created_at` DESC LIMIT ". $lim . " OFFSET ". $offs);
    return $result->rows;
  }

  public function deleteVacancy($id){
    $this->db->query("DELETE FROM " . DB_PREFIX . "vacancy_translate WHERE vacancy_id = '" . (int)$id . "'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "vacancies WHERE vacancy_id = '" . (int)$id . "'");
  }

  public function getPretendent($offs, $lim){
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "challenger WHERE `status` != 'trash' ORDER BY `send_at` DESC LIMIT ". $lim . " OFFSET ". $offs);

    return $query->rows;
  }

  public function getChallengerCount(){
    $result = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "challenger WHERE `status` != 'trash'");
    return (int)$result->rows[0]["COUNT(*)"];
  }

  public function getChallenger($id){
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "challenger WHERE `challenger_id` = ". $id);

    return $query->rows;
  }

  public function updateChallenger($id, $status){
    $query = $this->db->query("UPDATE " . DB_PREFIX . "challenger SET status='" . $status . "' WHERE challenger_id = '" . $id."'");
    return true;
  }

  public function getTrashChallengerCount(){
    $result = $this->db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "challenger WHERE `status` = 'trash'");
    return (int)$result->rows[0]["COUNT(*)"];
  }

  public function getTrashPretendent($offs, $lim){
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "challenger WHERE `status` = 'trash' ORDER BY `send_at` DESC LIMIT ". $lim . " OFFSET ". $offs);

    return $query->rows;
  }
}

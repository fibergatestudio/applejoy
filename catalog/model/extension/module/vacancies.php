<?php
class ModelExtensionModuleVacancies extends Model {

  public function getLastVacancies($lang_id){
    $res = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancies WHERE status != 'Trash' ORDER BY `created_at` DESC");
    $result = [];
    foreach($res->rows as $item_row){
      $lang_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "vacancy_translate WHERE vacancy_id = '" . $item_row["vacancy_id"] . "' AND language_id = '" . $lang_id ."'");
      if(!empty($lang_data->rows)){
        $temp_data = $lang_data->rows[0];
        $item_row["title"] = $temp_data["vacancy_title"];
        $item_row["description"] = html_entity_decode($temp_data["vacancy_description"]);
        $item_row["short_description"] = $temp_data["vacancy_short_description"];
      }
      $result[] = $item_row;
    }
    return $result;
  }

  public function saveSubscribe($data){
    $sql = "INSERT INTO " . DB_PREFIX . "challenger SET fullname = '" .$this->db->escape($data['fullname'])."', ";
    $sql .= " contact = '" . $this->db->escape($data["contact"]) . "', comment = '" .
    $this->db->escape($data["comment"]) . "'";
    $this->db->query($sql);
    $last_id = $this->db->getlastid();
    return $last_id;
  }
}

<?php

class ModelExtensionModuleOcdHreflang extends Model {

    public function getLanguageByCode($code) {
        $query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->db->escape($code) . "'");

        $language_id = $this->config->get('config_language_id');

        if ($query->num_rows) {
            $language_id = $query->row['language_id'];
        }

        return $language_id;
    }
}
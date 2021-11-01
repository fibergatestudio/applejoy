<?php

class ModelExtensionModuleOcdHreflang extends Model {

    public function setSeoUrl($data) {
        if (isset($data)) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query LIKE '%common/home%'");

            foreach ($data as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'common/home', keyword = '" . $this->db->escape($keyword) . "'");
                }
            }
        }

        if ($this->config->get('config_seo_pro')) {
            $this->cache->delete('seopro');
        }
    }

    public function getSeoUrl() {
        $result = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'common/home'");

        if ($query->rows) {
            foreach ($query->rows as $row) {
                $result[$row['store_id']][$row['language_id']] = $row['keyword'];
            }
        }

        return $result;
    }
}
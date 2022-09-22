<?php

namespace Opencart\Catalog\Controller\Tool;

class Fix extends \Opencart\System\Engine\Controller {
    public function company_qr_code() :void {
        
        // delete all qr_code images
        $files = glob(DIR_STORAGE . 'qr_codes/*');
        foreach($files as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }
        
        $companies_query = $this->db->query("SELECT
		company_code
		FROM `" . DB_PREFIX . "company`
		");
        
        // delete all company_code seo urls
        $this->db->query("DELETE
        FROM `" . DB_PREFIX . "seo_url`
        WHERE `key` = 'company_code'
		");
        
        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        
        foreach ($companies_query->rows as $company) {
            
            $company_code = $company['company_code'];
            
            foreach ($languages as $language) {
                $this->db->query(
    				"INSERT INTO
    					`" . DB_PREFIX . "seo_url`
    				SET
    					`store_id` = '0',
    					`language_id` = '" . (int)$language['language_id'] . "',
    					`key` = 'company_code',
    					`value` = '" . $this->db->escape($company_code) . "',
    					`keyword` = '" . $this->db->escape($company_code) . "',
    					`sort_order` = '10'
    			");
            }
            
            $this->load->controller('account/company|generateQrCode', $company_code);
            
        }
        
    }
}
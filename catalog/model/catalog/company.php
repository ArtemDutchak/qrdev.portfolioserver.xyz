<?php
namespace Opencart\Catalog\Model\Catalog;
class Company extends \Opencart\System\Engine\Model {

	public function getCompanyByCode(string $company_code): array {
		$query = $this->db->query(
			"SELECT
			c.company_id,
			c.company_name,
			c.image
			FROM `" . DB_PREFIX . "company` c 
			WHERE c.`company_code` = '" . $this->db->escape($company_code) . "'
			AND c.`status` = '1'");

		return $query->row;
	}
	
	public function convertQrLink() {
	    return 12123123;
	}

}

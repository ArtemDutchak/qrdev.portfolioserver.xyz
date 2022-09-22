<?php
namespace Opencart\Catalog\Model\Catalog;
class Company extends \Opencart\System\Engine\Model {

	public function getCompanyByCode(string $company_code): array {
		$query = $this->db->query(
			"SELECT
			c.company_id,
			c.company_name,
			c.settings,
			c.status,
			c.image
			FROM `" . DB_PREFIX . "company` c 
			WHERE c.`company_code` = '" . $this->db->escape($company_code) . "'
			");

		return $query->row;
	}

}

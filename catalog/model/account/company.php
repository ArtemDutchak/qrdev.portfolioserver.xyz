<?php
namespace Opencart\Catalog\Model\Account;
class Company extends \Opencart\System\Engine\Model {
	
	public function addCompany(array $data): int {
		
		$company_name = $this->db->escape((string)$data['company_name']);
		$company_code = md5($data['customer_id'] . $company_name . time());

		$this->db->query("INSERT INTO `" . DB_PREFIX . "company` SET
		`customer_id` = '" . (int)$data['customer_id'] . "',
		`image` = '" . $this->db->escape((string)$data['image']) . "',
		`company_name` = '" . $company_name . "',
		`status` = '" . (int)$data['status'] . "',
		`company_code` = '" . $company_code . "',
		`settings` = '" . json_encode($data['settings']) . "',
		`date_added` = NOW(),
		`date_modified` = NOW()");

		$company_id = $this->db->getLastId();

		return $company_id;
	}
	
	public function editCompany(array $data): void {

		$this->db->query("UPDATE `" . DB_PREFIX . "company` SET
		`image` = '" . $this->db->escape((string)$data['image']) . "',
		`company_name` = '" . $this->db->escape((string)$data['company_name']) . "',
		`status` = '" . (int)$data['status'] . "',
		`settings` = '" . json_encode($data['settings']) . "',
		`date_modified` = NOW()
		WHERE `company_id` = '" . (int)$data['company_id'] . "'
		");
		
	}
	
	public function addCompanyImage(string $company_code, string $filename): void {

		$this->db->query("UPDATE `" . DB_PREFIX . "company` SET
		`image` = '" . $filename . "'
		WHERE `company_code` = '" . $company_code . "'
		");
		
	}
	
}

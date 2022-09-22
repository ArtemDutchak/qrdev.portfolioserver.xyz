<?php
namespace Opencart\Catalog\Model\Account;
use \Opencart\System\Helper as Helper;
class Company extends \Opencart\System\Engine\Model {
	
	public function addCompany(array $data): int {
		
		$company_name = $this->db->escape((string)$data['company_name']);
		$company_code = $this->getUniqueCode();

		$this->db->query("INSERT INTO `" . DB_PREFIX . "company` SET
		`customer_id` = '" . (int)$data['customer_id'] . "',
		`company_name` = '" . $company_name . "',
		`status` = '" . (int)$data['status'] . "',
		`company_code` = '" . $company_code . "',
		`settings` = '" . $this->db->escape(json_encode($data['settings'],JSON_UNESCAPED_UNICODE)) . "',
		`date_added` = NOW(),
		`date_modified` = NOW()");

		$company_id = $this->db->getLastId();
		
		$languages = $this->getLanguages();
		
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
		
		return $company_id;
	}
	
	public function getUniqueCode(): string {
		
		$code = $this->generateUniqueCode();
		
		while(!$code){
			$code = $this->generateUniqueCode();
		}
		
		return $code;
	}
	
	public function generateUniqueCode(): string {
		
		$code = Helper\General\token(3);
		
		$query = $this->db->query(
			"SELECT company_id
			FROM `" . DB_PREFIX . "company`
			WHERE `company_code` = '" . $code . "'
		");
		
		if ($query->row) {
			return '';
		}
		
		return $code;
	}
	
	public function editCompany(array $data): void {

		$this->db->query("UPDATE `" . DB_PREFIX . "company` SET
		`company_name` = '" . $this->db->escape((string)$data['company_name']) . "',
		`status` = '" . (int)$data['status'] . "',
		`settings` = '" . $this->db->escape(json_encode($data['settings'],JSON_UNESCAPED_UNICODE)) . "',
		`date_modified` = NOW()
		WHERE `company_id` = '" . (int)$data['company_id'] . "'
		");
		
	}
	
	public function getAverageRate(int $company_id): float {

		$query = $this->db->query(
			"SELECT AVG(rating) as average
			FROM `" . DB_PREFIX . "review`
			WHERE `product_id` = '" . $company_id . "'
		");
		
		if ($query->row && $query->row['average']) {
			return round($query->row['average'], 2);
		}
		
		return 0.00;
		
	}
	
	public function getCompanyInfo(int $customer_id, int $company_id): array {
		$query = $this->db->query("SELECT
		*
		FROM `" . DB_PREFIX . "company`
		WHERE
		`company_id` = '" . $company_id . "'
		AND `customer_id` = '" . $customer_id . "'
		");

		return $query->row;
	}
	
	public function addCompanyImage(string $company_code, string $filename): void {

		$this->db->query("UPDATE `" . DB_PREFIX . "company` SET
		`image` = '" . $filename . "'
		WHERE `company_code` = '" . $company_code . "'
		");
		
	}

	public function getLanguages(): array {
		$language_data = $this->cache->get('catalog.language');

		if (!$language_data) {
			$language_data = [];

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE `status` = '1' ORDER BY `sort_order`, `name`");

			foreach ($query->rows as $result) {
				$image = HTTP_SERVER;

				if (!$result['extension']) {
					$image .= 'catalog/';
				} else {
					$image .= 'extension/' . $result['extension'] . '/catalog/';
				}

				$language_data[] = [
					'language_id' => $result['language_id'],
					'name'        => $result['name'],
					'code'        => $result['code'],
					'image'       => $image . 'language/' . $result['code'] . '/' . $result['code'] . '.png',
					'locale'      => $result['locale'],
					'extension'   => $result['extension'],
					'sort_order'  => $result['sort_order'],
					'status'      => $result['status']
				];
			}

			$this->cache->set('catalog.language', $language_data);
		}

		return $language_data;
	}
	
}

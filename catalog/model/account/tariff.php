<?php
namespace Opencart\Catalog\Model\Account;
class Tariff extends \Opencart\System\Engine\Model {
	
	public function addTariff(array $data): int {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_tariff` SET
		`customer_id` = '" . (int)$data['customer_id'] . "',
		`tariff_id` = '" . (int)$data['tariff_id'] . "',
		`active_to` = '" . $data['active_to'] . "',
		`date_activated` = '" . $data['date_activated'] . "'
		");

		$id = $this->db->getLastId();

		return $id;
	}
	
	public function editTariff(array $data): void {

		$this->db->query("UPDATE `" . DB_PREFIX . "customer_tariff` SET
		`tariff_id` = '" . (int)$data['tariff_id'] . "',
		`active_to` = '" . $data['active_to'] . "',
		`date_activated` = '" . $data['date_activated'] . "'
		WHERE
		`customer_id` = '" . (int)$data['customer_id'] . "'
		");
		
	}
	
	public function getUserTariff(int $customer_id): array {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_tariff`
		WHERE
		`customer_id` = '" . $customer_id . "'
		");
		
		return $query->row;
		
	}
	
	public function getTariffs(): array {

		$query = $this->db->query(
			"SELECT
				*
			FROM `" . DB_PREFIX . "tariff` t
			LEFT JOIN `" . DB_PREFIX . "tariff_description` td ON (t.`tariff_id` = td.`tariff_id`)
			WHERE
				t.`status` = '1'
				AND td.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
			ORDER BY
				t.sort_order
			ASC
		");
		
		return $query->rows;
		
	}
	
	public function getTariff(int $tariff_id): array {

		$query = $this->db->query(
			"SELECT
				*
			FROM `" . DB_PREFIX . "tariff` t
			LEFT JOIN `" . DB_PREFIX . "tariff_description` td ON (t.`tariff_id` = td.`tariff_id`)
			WHERE
				t.`status` = '1'
				AND t.`tariff_id` = '" . (int)$tariff_id . "'
				AND td.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
		");
		
		return $query->row;
		
	}
	
}

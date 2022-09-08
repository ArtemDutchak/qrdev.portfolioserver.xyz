<?php
namespace Opencart\Admin\Model\Catalog;
class Tariff extends \Opencart\System\Engine\Model {

	public function editTariff(int $tariff_id, array $data): void {
		
		$this->db->query(
			"UPDATE
				`" . DB_PREFIX . "tariff`
			SET
				`price` = '" . (int)$data['price'] . "',
				`companies` = '" . (int)$data['companies'] . "',
				`sort_order` = '" . (int)$data['sort_order'] . "',
				`status` = '" . (bool)$data['status'] . "',
				`date_modified` = NOW()
			WHERE
				`tariff_id` = '" . (int)$tariff_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "tariff_description` WHERE tariff_id = '" . (int)$tariff_id . "'");

		foreach ($data['tariff_description'] as $language_id => $value) {
			$this->db->query(
				"INSERT INTO
					`" . DB_PREFIX . "tariff_description`
				SET
					`tariff_id` = '" . (int)$tariff_id . "',
					`language_id` = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "'
				");
		}
		
	}

	public function getTariff(int $tariff_id): array {
		$query = $this->db->query(
			"SELECT
				*
			FROM `" . DB_PREFIX . "tariff` t
				LEFT JOIN `" . DB_PREFIX . "tariff_description` td ON (t.`tariff_id` = td.`tariff_id`)
			WHERE
				t.`tariff_id` = '" . $tariff_id . "'
				AND td.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
			");

		return $query->row;
	}

	public function getTariffs(array $data = []): array {
		$sql = "SELECT
			t.`tariff_id` AS `tariff_id`,
			td.`name` AS `name`,
			t.`price` AS `price`,
			t.`companies` AS `companies`,
			t.`sort_order` AS `sort_order`
		FROM `" . DB_PREFIX . "tariff` t
			LEFT JOIN `" . DB_PREFIX . "tariff_description` td ON (t.`tariff_id` = td.`tariff_id`)
		WHERE
			td.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
		";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getDescriptions(int $tariff_id): array {
		$tariff_description_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "tariff_description` WHERE `tariff_id` = '" . (int)$tariff_id . "'");

		foreach ($query->rows as $result) {
			$tariff_description_data[$result['language_id']] = [
				'name'             => $result['name'],
			];
		}

		return $tariff_description_data;
	}

	public function getTotalTariffs(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "tariff`");

		return (int)$query->row['total'];
	}
	
}

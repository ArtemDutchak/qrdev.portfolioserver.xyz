<?php
namespace Opencart\Catalog\Model\Account;
class Transaction extends \Opencart\System\Engine\Model {

	public function addTransaction(int $customer_id, string $description = '', float $amount = 0, int $order_id = 0): void {
		$this->db->query(
			"INSERT INTO `" . DB_PREFIX . "customer_transaction`
			SET
				`customer_id` = '" . (int)$customer_id . "',
				`order_id` = '" . (int)$order_id . "',
				`description` = '" . $this->db->escape($description) . "',
				`amount` = '" . (float)$amount . "',
				`date_added` = NOW()"
			);
	}

	public function getTransactions(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE `customer_id` = '" . (int)$this->customer->getId() . "'";

		$sort_data = [
			'amount',
			'description',
			'date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY `" . $data['sort'] . "`";
		} else {
			$sql .= " ORDER BY `date_added`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalTransactions(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "customer_transaction` WHERE `customer_id` = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}

	public function getTotalAmount(): int {
		$query = $this->db->query("SELECT SUM(amount) AS `total` FROM `" . DB_PREFIX . "customer_transaction` WHERE `customer_id` = '" . (int)$this->customer->getId() . "' GROUP BY `customer_id`");

		if ($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}
}

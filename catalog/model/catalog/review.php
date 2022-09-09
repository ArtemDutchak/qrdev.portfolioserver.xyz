<?php
namespace Opencart\Catalog\Model\Catalog;
class Review extends \Opencart\System\Engine\Model {
	public function addReview(int $product_id, array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "review` SET `author` = '" . $this->db->escape((string)$data['name']) . "', `customer_id` = '" . (int)$this->customer->getId() . "', `product_id` = '" . (int)$product_id . "', `text` = '" . $this->db->escape((string)$data['text']) . "', `rating` = '" . (int)$data['rating'] . "', `date_added` = NOW()");

		return $this->db->getLastId();
	}
	
	public function addCompanyReview(string $company_id, array $data): int {
		$this->db->query(
			"INSERT INTO `" . DB_PREFIX . "review`
			SET
			`author` = '" . $this->db->escape((string)$data['name']) . "',
			`customer_id` = '0',
			`product_id` = '" . (int)$company_id . "',
			`text` = '" . $this->db->escape((string)$data['text']) . "',
			`telephone` = '" . $this->db->escape((string)$data['telephone']) . "',
			`email` = '" . $this->db->escape((string)$data['email']) . "',
			`rating` = '" . (int)$data['rating'] . "',
			`status` = '1',
			`date_modified` = '" . $this->db->escape((string)$data['date_added']) . "',
			`date_added` = '" . $this->db->escape((string)$data['date_added']) . "'"
		);

		return $this->db->getLastId();
	}

	public function getReviewsByProductId(int $product_id, int $start = 0, int $limit = 20): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.`author`, r.`rating`, r.`text`, r.`date_added` FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product` p ON (r.`product_id` = p.`product_id`) LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) WHERE r.`product_id` = '" . (int)$product_id . "' AND p.`date_available` <= NOW() AND p.`status` = '1' AND r.`status` = '1' AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.`date_added` DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReviewsByProductId(int $product_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product` p ON (r.`product_id` = p.`product_id`) LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) WHERE p.`product_id` = '" . (int)$product_id . "' AND p.`date_available` <= NOW() AND p.`status` = '1' AND r.`status` = '1' AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return (int)$query->row['total'];
	}

	public function getReviewsByCompanyId(int $company_id, int $start = 0, int $limit = 20): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT
			r.`review_id`,
			r.`author`,
			r.`rating`,
			r.`text`,
			r.`telephone`,
			r.`email`,
			r.`date_added`
			FROM
			`" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "company` c ON (r.`product_id` = c.`company_id`)
			WHERE
			r.`product_id` = '" . (int)$company_id . "'
			AND c.`status` = '1'
			ORDER BY
			r.`date_added` DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReviewsByCompanyId(int $company_id): int {
		$query = $this->db->query("SELECT
			COUNT(*) AS `total`
			FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "company` c ON (r.`product_id` = c.`company_id`)
			WHERE
			r.`product_id` = '" . (int)$company_id . "'
			AND c.`status` = '1'");

		return $query->row['total'];
	}

	public function setStatus(array $data): void {
		
		$query = $this->db->query("UPDATE `" . DB_PREFIX . "review` 
		SET
			`status` = '" . (int)$data['status_id'] . "'
		WHERE
			`review_id` = '" . (int)$data['review_id'] . "'
		");
		
	}
}

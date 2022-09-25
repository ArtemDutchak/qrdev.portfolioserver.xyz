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
	public function addReviewImage(int $review_id, string $path): int {
		$this->db->query(
			"INSERT INTO `" . DB_PREFIX . "review_image`
			SET
			`review_id` = '" . $review_id . "',
			`image` = '" . $path . "'"
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

		$query = $this->db->query("SELECT r.`review_id`, r.`author`, r.`rating`, r.`text`, r.`date_added` FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product` p ON (r.`product_id` = p.`product_id`) LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) WHERE r.`product_id` = '" . (int)$product_id . "' AND p.`date_available` <= NOW() AND p.`status` = '1' AND r.`status` = '1' AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.`date_added` DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReviewsByProductId(int $product_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product` p ON (r.`product_id` = p.`product_id`) LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) WHERE p.`product_id` = '" . (int)$product_id . "' AND p.`date_available` <= NOW() AND p.`status` = '1' AND r.`status` = '1' AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return (int)$query->row['total'];
	}

	public function getReview(int $review_id): array {
		
		$sql = "SELECT
			r.`review_id`,
			r.`author`,
			r.`rating`,
			r.`text`,
			r.`telephone`,
			r.`email`,
			r.`date_added`
			FROM
			`" . DB_PREFIX . "review` r
			LEFT JOIN `" . DB_PREFIX . "company` c ON (r.`product_id` = c.`company_id`)
			WHERE
			c.`customer_id` = '" . (int)$this->customer->getId() . "'
			AND c.`status` = '1'";
			
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function getReviews(array $filter_data): array {
		if ($filter_data['start'] < 0) {
			$filter_data['start'] = 0;
		}

		if ($filter_data['limit'] < 1) {
			$filter_data['limit'] = 20;
		}
		
		$sql = "SELECT
			r.`review_id`,
			r.`author`,
			r.`rating`,
			r.`text`,
			r.`telephone`,
			r.`email`,
			r.`status`,
			r.`date_added`
			FROM
			`" . DB_PREFIX . "review` r
			LEFT JOIN `" . DB_PREFIX . "company` c ON (r.`product_id` = c.`company_id`)
			WHERE
			r.`product_id` = '" . (int)$filter_data['company_id'] . "'
			AND r.`status` <> '0'
			AND c.`status` = '1'";
			
		if ($filter_data['sort']) {
			
			if ($filter_data['sort'] === 'more_three') {
				$sql .= " AND r.`rating` > '3'";
			}elseif ($filter_data['sort'] === 'less_four') {
				$sql .= " AND r.`rating` < '4'";
			}elseif ($filter_data['sort'] === 'solved') {
				$solved_status_id = 2;
				$sql .= " AND r.`status` = '" . $solved_status_id . "'";
			}elseif ($filter_data['sort'] === 'new') {
				$new_status_id = 1;
				$sql .= " AND r.`status` = '" . $new_status_id . "'";
			}
			
		}
			
		$sql .= " ORDER BY
			r.`date_added` DESC LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalReviews(array $filter_data): int {
		
		$sql = "SELECT
			COUNT(*) AS `total`
			FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "company` c ON (r.`product_id` = c.`company_id`)
			WHERE
			r.`product_id` = '" . (int)$filter_data['company_id'] . "'
			AND r.`status` <> '0'
			AND c.`status` = '1'";
		
		if ($filter_data['sort']) {
			
			if ($filter_data['sort'] === 'more_three') {
				$sql .= " AND r.`rating` > '3'";
			}elseif ($filter_data['sort'] === 'less_four') {
				$sql .= " AND r.`rating` < '4'";
			}elseif ($filter_data['sort'] === 'solved') {
				$solved_status_id = 2;
				$sql .= " AND r.`status` = '" . $solved_status_id . "'";
			}elseif ($filter_data['sort'] === 'new') {
				$new_status_id = 1;
				$sql .= " AND r.`status` = '" . $new_status_id . "'";
			}
			
		}
			
		$query = $this->db->query($sql);

		return $query->row['total'];
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

	public function getReviewRates(int $company_id): array {
		
		$query = $this->db->query("SELECT
			r.`rating`
			FROM `" . DB_PREFIX . "review` r
			WHERE
			r.`product_id` = '" . (int)$company_id . "'
			AND r.`status` <> '0'");

		return $query->rows;
		
	}

	public function getExpiredReviews(int $company_id): array {
		
		$query = $this->db->query("SELECT
		ct.active_to as active_to
		FROM `" . DB_PREFIX . "customer_tariff` ct
		WHERE
		`customer_id` = '" . (int)$this->customer->getId() . "'
		");
		
		if (!$query->row) {
			return [];
		}
		
		$query = $this->db->query("SELECT
			*
			FROM `" . DB_PREFIX . "review` r
			WHERE
			r.`product_id` = '" . (int)$company_id . "'
			AND r.`date_added` > '" . $query->row['active_to'] . "'
			AND r.`status` <> '0'");

		return $query->rows;
		
	}
	
}

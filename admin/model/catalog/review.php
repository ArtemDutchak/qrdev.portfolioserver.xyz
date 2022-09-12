<?php
namespace Opencart\Admin\Model\Catalog;
class Review extends \Opencart\System\Engine\Model {
	public function addReview(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "review` SET
		`author` = '" . $this->db->escape((string)$data['author']) . "',
		`product_id` = '" . (int)$data['product_id'] . "',
		`text` = '" . $this->db->escape(strip_tags((string)$data['text'])) . "',
		`telephone` = '" . $this->db->escape(strip_tags((string)$data['telephone'])) . "',
		`email` = '" . $this->db->escape(strip_tags((string)$data['email'])) . "',
		`rating` = '" . (int)$data['rating'] . "',
		`status` = '" . $data['status'] . "',
		`date_added` = '" . $this->db->escape((string)$data['date_added']) . "'");

		$review_id = $this->db->getLastId();

		$this->cache->delete('product');

		return $review_id;
	}

	public function editReview(int $review_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "review` SET
		`author` = '" . $this->db->escape((string)$data['author']) . "',
		`product_id` = '" . (int)$data['product_id'] . "',
		`text` = '" . $this->db->escape(strip_tags((string)$data['text'])) . "',
		`telephone` = '" . $this->db->escape(strip_tags((string)$data['telephone'])) . "',
		`email` = '" . $this->db->escape(strip_tags((string)$data['email'])) . "',
		`rating` = '" . (int)$data['rating'] . "',
		`status` = '" . $data['status'] . "',
		`date_added` = '" . $this->db->escape((string)$data['date_added']) . "',
		`date_modified` = NOW()
		WHERE
		`review_id` = '" . (int)$review_id . "'");

		$this->cache->delete('product');
	}

	public function deleteReview(int $review_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "review` WHERE `review_id` = '" . (int)$review_id . "'");

		$this->cache->delete('product');
	}

	public function getReview(int $review_id): array {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT pd.`name` FROM `" . DB_PREFIX . "product_description` pd WHERE pd.`product_id` = r.`product_id` AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "') AS product FROM `" . DB_PREFIX . "review` r WHERE r.`review_id` = '" . (int)$review_id . "'");

		return $query->row;
	}

	public function getCompanyReview(int $review_id): array {
		$query = $this->db->query("SELECT
			DISTINCT *,
				(SELECT c.`company_name` FROM `" . DB_PREFIX . "company` c
				WHERE c.`company_id` = r.`product_id`) AS company
			FROM `" . DB_PREFIX . "review` r WHERE r.`review_id` = '" . (int)$review_id . "'");

		return $query->row;
	}

	public function getReviews(array $data = []): array {
		$sql = "SELECT r.`review_id`, pd.`name`, r.`author`, r.`rating`, r.`status`, r.`date_added` FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (r.`product_id` = pd.`product_id`) WHERE pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.`name` LIKE '" . $this->db->escape((string)$data['filter_product'] . '%') . "'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.`author` LIKE '" . $this->db->escape((string)$data['filter_author'] . '%') . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND r.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.`date_added`) = DATE('" . $this->db->escape((string)$data['filter_date_added']) . "')";
		}

		$sort_data = [
			'pd.name',
			'r.author',
			'r.rating',
			'r.status',
			'r.date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.`date_added`";
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

	public function getTotalReviews(array $data = []): int {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "review` r LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (r.`product_id` = pd.`product_id`) WHERE pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.`name` LIKE '" . $this->db->escape((string)$data['filter_product'] . '%') . "'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.`author` LIKE '" . $this->db->escape((string)$data['filter_author'] . '%') . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND r.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.`date_added`) = DATE('" . $this->db->escape((string)$data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}

	public function getCompaniesReviews(array $data = []): array {
		$sql = "SELECT
			r.`review_id`,
			c.`company_name` as 'name',
			r.`author`,
			r.`rating`,
			r.`status`,
			rs.`name` as 'status_name',
			r.`date_added`
		FROM `" . DB_PREFIX . "review` r
		LEFT JOIN `" . DB_PREFIX . "company` c ON (r.`product_id` = c.`company_id`)
		LEFT JOIN `" . DB_PREFIX . "review_status` rs ON (r.`status` = rs.`review_status_id`)
		WHERE
			rs.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
		";

		if (!empty($data['filter_product'])) {
			$sql .= " AND c.`company_name` LIKE '" . $this->db->escape((string)$data['filter_product'] . '%') . "'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.`author` LIKE '" . $this->db->escape((string)$data['filter_author'] . '%') . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND r.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.`date_added`) = DATE('" . $this->db->escape((string)$data['filter_date_added']) . "')";
		}

		$sort_data = [
			'c.company_name',
			'r.author',
			'r.rating',
			'r.status',
			'r.date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.`date_added`";
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

	public function getTotalCompaniesReviews(array $data = []): int {
		$sql = "SELECT COUNT(*) AS `total`
		FROM `" . DB_PREFIX . "review` r
		LEFT JOIN `" . DB_PREFIX . "company` c ON (r.`product_id` = c.`company_id`)
		WHERE c.`company_id` > '0'";

		if (!empty($data['filter_product'])) {
			$sql .= " AND c.`company_name` LIKE '" . $this->db->escape((string)$data['filter_product'] . '%') . "'";
		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND r.`author` LIKE '" . $this->db->escape((string)$data['filter_author'] . '%') . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND r.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.`date_added`) = DATE('" . $this->db->escape((string)$data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}

	public function getReviewsStatuses(): array {
		$query = $this->db->query("SELECT 
			rs.`review_status_id`,
			rs.`name`
		FROM `" . DB_PREFIX . "review_status` rs
		WHERE
			`language_id` = '" . (int)$this->config->get('config_language_id') . "'
		");

		return $query->rows;
	}

	public function getTotalReviewsAwaitingApproval(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "review` WHERE `status` = '0'");

		return (int)$query->row['total'];
	}
}

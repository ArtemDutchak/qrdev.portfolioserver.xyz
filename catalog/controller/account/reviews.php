<?php
namespace Opencart\Catalog\Controller\Account;
class Reviews extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('account/reviews');

		$this->load->model('tool/image');

		if (!$this->customer->isLogged()) {
			
			$this->response->redirect($this->url->link('account/login'));
			
		}		
		
		$companies = $this->customer->getActiveCompanyList();
		
		$data['companies'] = array();
		
		foreach ($companies as $result) {
			
			if ($result['image'] && is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
				$thumb = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), 38, 38);
			} else {
				$thumb = '';
			}
			
			$company = array(
				'id' => $result['company_id'],
				'name' => $result['company_name'],
				'thumb' => $thumb,
				'href' => $this->url->link('account/reviews', 'company_id=' . $result['company_id']),
			);
			
			$data['companies'][] = $company;
			
		}
		
		$data['company_id'] = 0;
		if (!empty($this->request->get['company_id'])) {
			$data['company_id'] = (int)$this->request->get['company_id'];
		}
		if (!$data['company_id'] && $data['companies']) {
			$data['company_id'] = (int)$data['companies'][0]['id'];
		}
		if (!$data['company_id']) {
			$this->response->redirect($this->url->link('account/company|list', 'customer_token=' . $this->session->data['customer_token'], true));
		}
		
		// $company_info = $this->model_catalog_company->getCompany($data['company_id']);
		
		$this->load->model('catalog/review');

		// $sort = 'more_three' || 'less_four' || 'solved';
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = '';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = [];
		
		$reviews_per_page = 6;

		$filter_data = [
			'company_id'          => $data['company_id'],
			'sort'                => $sort,
			'start'               => ($page - 1) * $reviews_per_page,
			'limit'               => $reviews_per_page
		];
		
		$review_total = $this->model_catalog_review->getTotalReviews($filter_data);
		$results = $this->model_catalog_review->getReviews($filter_data);
		
		foreach ($results as $result) {
			$data['reviews'][] = [
				'id'         => $result['review_id'],
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'telephone'  => nl2br($result['telephone']),
				'email'      => nl2br($result['email']),
				'date'       => date("d.m.Y", strtotime($result['date_added'])),
				'time'       => date("H:i", strtotime($result['date_added'])),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			];
		}

		$url = '&customer_token=' . $this->session->data['customer_token'];

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $review_total,
			'page'  => $page,
			'limit' => $reviews_per_page,
			'url'   => $this->url->link('account/reviews', 'company_id=' . $data['company_id'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $reviews_per_page) + 1 : 0, ((($page - 1) * $reviews_per_page) > ($review_total - $reviews_per_page)) ? $review_total : ((($page - 1) * $reviews_per_page) + $reviews_per_page), $review_total, ceil($review_total / $reviews_per_page));
		
		$data['active_sort'] = $sort;
		
		$data['sorts'] = [];

		$data['sorts'][] = [
			'text'  => $this->language->get('text_more_three'),
			'value' => 'more_three',
			'href'  => $this->url->link('account/reviews', 'company_id=' . $data['company_id'] . '&customer_token=' . $this->session->data['customer_token'] . '&sort=more_three'),
		];

		$data['sorts'][] = [
			'text'  => $this->language->get('text_less_four'),
			'value' => 'less_four',
			'href'  => $this->url->link('account/reviews', 'company_id=' . $data['company_id'] . '&customer_token=' . $this->session->data['customer_token'] . '&sort=less_four'),
		];

		$data['sorts'][] = [
			'text'  => $this->language->get('text_solved'),
			'value' => 'solved',
			'href'  => $this->url->link('account/reviews', 'company_id=' . $data['company_id'] . '&customer_token=' . $this->session->data['customer_token'] . '&sort=solved'),
		];
		
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/reviews', $data));
	}
}
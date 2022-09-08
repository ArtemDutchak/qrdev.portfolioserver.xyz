<?php
namespace Opencart\Admin\Controller\Catalog;
use \Opencart\System\Helper as Helper;
class Tariff extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('catalog/tariff');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/tariff', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['repair'] = $this->url->link('catalog/tariff|repair', 'user_token=' . $this->session->data['user_token']);
		$data['add'] = $this->url->link('catalog/tariff|form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('catalog/tariff|delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/tariff ', $data));
	}

	public function list(): void {
		$this->load->language('catalog/tariff');

		$this->response->setOutput($this->getList());
	}

	protected function getList(): string {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['action'] = $this->url->link('catalog/tariff|list', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['tariffs'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('catalog/tariff');

		$tariff_total = $this->model_catalog_tariff->getTotalTariffs();

		$results = $this->model_catalog_tariff->getTariffs($filter_data);

		foreach ($results as $result) {
			$data['tariffs'][] = [
				'tariff_id' => $result['tariff_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'edit'        => $this->url->link('catalog/tariff|form', 'user_token=' . $this->session->data['user_token'] . '&tariff_id=' . $result['tariff_id'] . $url)
			];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/tariff|list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
		$data['sort_sort_order'] = $this->url->link('catalog/tariff|list', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $tariff_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('catalog/tariff|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($tariff_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($tariff_total - $this->config->get('config_pagination_admin'))) ? $tariff_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $tariff_total, ceil($tariff_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('catalog/tariff_list', $data);
	}

	public function form(): void {
		$this->load->language('catalog/tariff');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');

		$data['text_form'] = !isset($this->request->get['tariff_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/tariff', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['save'] = $this->url->link('catalog/tariff|save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('catalog/tariff', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['tariff_id'])) {
			$this->load->model('catalog/tariff');

			$tariff_info = $this->model_catalog_tariff->getTariff($this->request->get['tariff_id']);
		}

		if (isset($this->request->get['tariff_id'])) {
			$data['tariff_id'] = (int)$this->request->get['tariff_id'];
		} else {
			$data['tariff_id'] = 0;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['tariff_id'])) {
			$data['tariff_description'] = $this->model_catalog_tariff->getDescriptions($this->request->get['tariff_id']);
		} else {
			$data['tariff_description'] = [];
		}

		if (!empty($tariff_info)) {
			$data['sort_order'] = $tariff_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (!empty($tariff_info)) {
			$data['price'] = $tariff_info['price'];
		} else {
			$data['price'] = 0;
		}

		if (!empty($tariff_info)) {
			$data['companies'] = $tariff_info['companies'];
		} else {
			$data['companies'] = 0;
		}

		if (!empty($tariff_info)) {
			$data['status'] = $tariff_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/tariff_form', $data));
	}

	public function save(): void {
		$this->load->language('catalog/tariff');

		$json = [];

		if (!$this->user->hasPermission('modify', 'catalog/tariff')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['tariff_description'] as $language_id => $value) {
			if ((Helper\Utf8\strlen(trim($value['name'])) < 1) || (Helper\Utf8\strlen($value['name']) > 255)) {
				$json['error']['name_' . $language_id] = $this->language->get('error_name');
			}
		}

		$this->load->model('catalog/tariff');

		if (isset($json['error']) && !isset($json['error']['warning'])) {
			$json['error']['warning'] = $this->language->get('error_warning');
		}

		if (!$json) {
			if (!$this->request->post['tariff_id']) {
				$json['tariff_id'] = $this->model_catalog_tariff->addTariff($this->request->post);
			} else {
				$this->model_catalog_tariff->editTariff($this->request->post['tariff_id'], $this->request->post);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function repair(): void {
		$this->load->language('catalog/tariff');

		$json = [];

		if (!$this->user->hasPermission('modify', 'catalog/tariff')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('catalog/tariff');

			$this->model_catalog_tariff->repairTariffs();

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete(): void {
		$this->load->language('catalog/tariff');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'catalog/tariff')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('catalog/tariff');

			foreach ($selected as $tariff_id) {
				$this->model_catalog_tariff->deleteTariff($tariff_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete(): void {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/tariff');

			$filter_data = [
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			];

			$results = $this->model_catalog_tariff->getTariffs($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'tariff_id' => $result['tariff_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				];
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
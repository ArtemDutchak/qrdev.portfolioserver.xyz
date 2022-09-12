<?php
namespace Opencart\Catalog\Controller\Common;
class HeaderHidden extends \Opencart\System\Engine\Controller {
	public function index(): string {
		// Analytics
		$data['analytics'] = [];

		if (!$this->config->get('config_cookie_id') || (isset($this->request->cookie['policy']) && $this->request->cookie['policy'])) {
			$this->load->model('setting/extension');

			$analytics = $this->model_setting_extension->getExtensionsByType('analytics');

			foreach ($analytics as $analytic) {
				if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
					$data['analytics'][] = $this->load->controller('extension/' . $analytic['extension'] . '/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
				}
			}
		}
		
		$this->load->language('common/header');
		
		$data['languages'] = array();
		$this->load->model('localisation/language');
		$results = $this->model_localisation_language->getLanguages();
		
		// if (!empty($this->session->data['language'])) {
		// 	$data['code'] = $this->session->data['language'];
		// }
		
		// $data['code'] = 'uk-ua';
		
		foreach ($results as $result) {
			
			if ($result['status']) {
				
				$data['languages'][] = array(
					'name' => $result['name'],
					'code' => $result['code']
				);
			}			
		}

		$data['lang'] = $this->language->get('code');
		
		if (!empty($_COOKIE['language'])) {
			$data['code'] = $_COOKIE['language'];
		}else{
			$data['code'] = $data['lang'];
		}
		
		$data['direction'] = $this->language->get('direction');
		
		$data['href_reviews'] = $this->url->link('account/reviews', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_my_companies'] = $this->url->link('account/company|list', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_tariffs'] = $this->url->link('account/tariffs', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_settings'] = $this->url->link('account/account', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_write_review'] = $this->url->link('account/account', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_feedback'] = $this->url->link('information/contacts', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_exit'] = $this->url->link('account/account', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));

		$data['title'] = $this->document->getTitle();
		$data['base'] = $this->config->get('config_url');
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();

		// Hard coding css so they can be replaced via the event's system.
		$data['bootstrap'] = 'catalog/view/stylesheet/bootstrap.css';
		$data['icons'] = 'catalog/view/stylesheet/fonts/fontawesome/css/all.min.css';
		$data['stylesheet'] = 'catalog/view/stylesheet/stylesheet.css';

		// Hard coding scripts so they can be replaced via the event's system.
		$data['jquery'] = 'catalog/view/javascript/jquery/jquery-3.6.0.min.js';

		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');

		$data['name'] = $this->config->get('config_name');

		return $this->load->view('common/header_hidden', $data);
	}
}

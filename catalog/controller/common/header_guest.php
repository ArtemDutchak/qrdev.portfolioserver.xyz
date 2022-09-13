<?php
namespace Opencart\Catalog\Controller\Common;
class HeaderGuest extends \Opencart\System\Engine\Controller {
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

		$data['href_social_telegram'] = 'https://telegram.org/';
		$data['href_social_facebook'] = 'https://facebook.com/';
		$data['href_social_twitter'] = 'https://twitter.com/';
		
		$data['href_login'] = $this->url->link('account/login');

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

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['home'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		$data['wishlist'] = $this->url->link('account/wishlist', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['logged'] = $this->customer->isLogged();
		
		if ($this->customer->isLogged()) {
			$data['account_button'] = $this->load->controller('common/account_button');
			$data['current_tariff'] = $this->load->controller('common/current_tariff');
		} else {
			$data['account_button'] = $this->load->controller('common/login_button');
			$data['current_tariff'] = '';
		}
		

		$data['shopping_cart'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
		$data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language'));
		$data['contact'] = $this->url->link('information/contact', 'language=' . $this->config->get('config_language'));
		$data['telephone'] = $this->config->get('config_telephone');

		// $data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/header_guest', $data);
	}
}

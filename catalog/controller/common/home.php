<?php
namespace Opencart\Catalog\Controller\Common;
class Home extends \Opencart\System\Engine\Controller {
	public function index(): void {
		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/reviews', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']));
		}

		$this->load->language('account/login');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->session->data['redirect'])) {
			$data['redirect'] = $this->session->data['redirect'];

			unset($this->session->data['redirect']);
		} elseif (isset($this->request->get['redirect'])) {
			$data['redirect'] = urldecode($this->request->get['redirect']);
		} else {
			$data['redirect'] = '';
		}
		

		$this->session->data['register_token'] = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
		$this->session->data['login_token'] = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
		
		$data['register_form_action'] = $this->url->link('account/register|register', 'register_token=' . $this->session->data['register_token']);
		$data['login_form_action'] = $this->url->link('account/login|login', 'login_token=' . $this->session->data['login_token']);
		$data['forgotten_form_action'] = $this->url->link('account/forgotten|confirm', 'login_token=' . $this->session->data['login_token']);

		$data['login'] = $this->url->link('account/login|login', 'language=' . $this->config->get('config_language') . '&login_token=' . $this->session->data['login_token']);
		$data['register'] = $this->url->link('account/register', 'language=' . $this->config->get('config_language'));
		$data['forgotten'] = $this->url->link('account/forgotten', 'language=' . $this->config->get('config_language'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header_guest');

		$this->response->setOutput($this->load->view('account/login', $data));
	}
	
	public function index_old(): void {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		
		if ($this->customer->isLogged()) {
			$data['header'] = $this->load->controller('common/header');
		}else{
			$data['header'] = $this->load->controller('common/header_guest');
		}

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
<?php
namespace Opencart\Catalog\Controller\Account;
class Success extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('account/success');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/success', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''))
		];

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_success'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
		}

		if ($this->cart->hasProducts()) {
			$data['continue'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
		} else {
			$data['continue'] = $this->url->link('account/account', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
	
	public function register(): void {
		
		if (!$this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/login'));
		}
		
		$this->load->language('account/register');
		$this->load->model('account/customer');
		
		$email = $this->customer->getEmail();
		$customer_info = $this->model_account_customer->getCustomerByEmail($email);
		
		if (!$customer_info['is_activated']) {
			
			$data['text_register_success'] = $this->language->get('text_register_success');
			$data['text_sent_email_for_activation'] = sprintf($this->language->get('text_sent_email_for_activation'), $email, $email);
			
			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('account/register_success', $data));
			return;
			
		}
		
		$this->response->redirect($this->url->link('account/account'));
		
	}
}
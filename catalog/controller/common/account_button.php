<?php
namespace Opencart\Catalog\Controller\Common;
class AccountButton extends \Opencart\System\Engine\Controller {
	public function index(): string {
		
		$this->load->language('common/header');
		
		$data['customer_name'] = $this->customer->getFirstName();
		
		$data['href_settings'] = $this->url->link('account/account', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_write_review'] = $this->url->link('account/account', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_feedback'] = $this->url->link('information/contacts', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['href_exit'] = $this->url->link('account/logout');

		return $this->load->view('common/account_button', $data);
	}
	
}
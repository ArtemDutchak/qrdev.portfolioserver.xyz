<?php
namespace Opencart\Catalog\Controller\Account;
use \Opencart\System\Helper as Helper;
class Account extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('account/edit');

		if (!$this->customer->isLogged() || (!isset($this->request->get['customer_token']) || !isset($this->session->data['customer_token']) || ($this->request->get['customer_token'] != $this->session->data['customer_token']))) {
			$this->session->data['redirect'] = $this->url->link('account/account');

			$this->response->redirect($this->url->link('account/login'));
		}
		
		$this->load->model('account/customer');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		
		$data['account_form_action']  = $this->url->link('account/account|edit');
		$data['password_form_action'] = $this->url->link('account/account|password');
		
		$data['name'] = $customer_info['firstname'];
		$data['email'] = $customer_info['email'];
		$data['telephone'] = $customer_info['telephone'];
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/edit', $data));
	}
	
	public function edit(): void {
		
		if (!$this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/login'));
		}
		
		$json = array(
			'errors' => array(),
		);
		
		$this->load->language('account/account');
		$this->load->language('account/edit');
		
		if (empty($this->request->post['name'])) {
			$json['errors'][] = array(
				'field_name' => 'name',
				'text' => $this->language->get('error_name'),
			);
		}
		
		if (empty($this->request->post['email'])) {
			$json['errors'][] = array(
				'field_name' => 'email',
				'text' => $this->language->get('error_email'),
			);
		} elseif ((Helper\Utf8\strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$json['errors'][] = array(
				'field_name' => 'email',
				'text' => $this->language->get('error_email'),
			);
		}
		
		if (empty($this->request->post['phone'])) {
			$json['errors'][] = array(
				'field_name' => 'phone',
				'text' => $this->language->get('error_telephone'),
			);
		}else{
			$telephone = preg_replace('/\D/', '', trim($this->request->post['phone']));
			$this->request->post['telephone']    = $telephone;
			if (Helper\Utf8\strlen($telephone) != 12) {
				$json['errors'][] = array(
					'field_name' => 'phone',
					'text' => $this->language->get('error_telephone'),
				);
			}
		}
		
		$this->load->model('account/customer');
		
		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$json['errors'][] = array(
				'field_name' => 'email',
				'text' => $this->language->get('error_exists'),
			);
		}
		
		if (!$json['errors']) {
		
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
			
			if (isset($this->request->post['name'])) {
				$customer_info['firstname'] = trim($this->request->post['name']);
			}
			if (isset($this->request->post['email'])) {
				$customer_info['email'] = trim($this->request->post['email']);
			}
			if (isset($this->request->post['phone'])) {
				$customer_info['telephone'] = trim($this->request->post['phone']);
			}
			
			$this->model_account_customer->editCustomer($this->customer->getId(), $customer_info);
			$json['msg'] = $this->language->get('text_success_edit');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function password(): void {
		
		if (!$this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/login'));
		}
		
		$json = array(
			'errors' => array(),
		);
		
		$this->load->language('account/account');
		$this->load->language('account/edit');
		
		$this->load->model('account/customer');
		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		
		if (empty($this->request->post['old_password'])) {
			$json['errors'][] = array(
				'field_name' => 'old_password',
				'text' => $this->language->get('error_empty_old_password'),
			);
		} elseif ( !password_verify($this->request->post['old_password'], $customer_info['password']) ) {
			$json['errors'][] = array(
				'field_name' => 'old_password',
				'text' => $this->language->get('error_password_mismatch'),
			);
		}
		
		if (empty($this->request->post['old_password'])) {
			$json['errors'][] = array(
				'field_name' => 'old_password',
				'text' => $this->language->get('error_empty_old_password'),
			);
		}
		
		if (empty($this->request->post['new_password'])) {
			$json['errors'][] = array(
				'field_name' => 'new_password',
				'text' => $this->language->get('error_empty_new_password'),
			);
		}
		
		if (empty($this->request->post['repeat_password'])) {
			$json['errors'][] = array(
				'field_name' => 'repeat_password',
				'text' => $this->language->get('error_empty_confirm'),
			);
		}
		
		if (
		   !empty($this->request->post['new_password'])
		&& !empty($this->request->post['repeat_password'])
		&& $this->request->post['new_password'] != $this->request->post['repeat_password']
			) {
			$json['errors'][] = array(
				'field_name' => 'repeat_password',
				'text' => $this->language->get('error_confirm'),
			);
		}
		
		if (!$json['errors']) {
			$customer_info = $this->model_account_customer->editPassword($customer_info['email'] , trim($this->request->post['new_password']));
			$json['msg'] = $this->language->get('text_success_edit');
		}
		
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
}

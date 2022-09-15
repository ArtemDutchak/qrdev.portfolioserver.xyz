<?php
namespace Opencart\Catalog\Controller\Account;
use \Opencart\System\Helper as Helper;
class Activation extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('account/register');
		
		if (!isset($this->request->get['c'])) {
			$this->response->redirect($this->url->link('error/not_found'));
		}
		
		$this->load->model('account/customer');
        $customer = $this->model_account_customer->getCustomerByActivationCode($this->request->get['c']);
		
		if (!$customer) {
			$this->response->redirect($this->url->link('error/not_found'));
		}
		
		if ($customer['is_activated']) {
			$data['text_activation'] = $this->language->get('text_already_activated');
		}else{
			$this->model_account_customer->setActivation($customer['customer_id'], 1);
			$data['text_activation'] = $this->language->get('text_activation_success');
		}
		
		$data['href_login'] = $this->url->link('account/login');
		
		$data['breadcrumbs'] = [];
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header_guest');

		$this->response->setOutput($this->load->view('account/activation_success', $data));
	}
	
}

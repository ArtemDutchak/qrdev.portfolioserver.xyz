<?php
namespace Opencart\Catalog\Controller\Information;
class Contacts extends \Opencart\System\Engine\Controller {
	public function index(): void {
		
		$this->load->language('information/contact');
		
		$data['telephone'] = $this->config->get('config_telephone');
		$data['telephone_digits'] = preg_replace('/\D/', '', $data['telephone']);
		$data['telegram'] = 'telegram';
		$data['email'] = $this->config->get('config_email');
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/contacts', $data));
		
	}
}
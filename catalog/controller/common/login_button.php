<?php
namespace Opencart\Catalog\Controller\Common;
class LoginButton extends \Opencart\System\Engine\Controller {
	public function index(): string {
		
		$this->load->language('common/header');
		
		$data['href_login'] = $this->url->link('account/login');

		return $this->load->view('common/login_button', $data);
	}
	
}
<?php
namespace Opencart\Catalog\Controller\Common;
class FooterHidden extends \Opencart\System\Engine\Controller {
	public function index(): string {
		$this->load->language('common/footer');
		
		if ($this->customer->isLogged()) {
			$data['href_home'] = $this->url->link('common/home', (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		}else{
			$data['href_home'] = $this->url->link('common/home');
		}
		
		$data['text_service_name'] = $this->config->get('config_name');
		
		$data['bootstrap'] = 'catalog/view/javascript/bootstrap/js/bootstrap.bundle.min.js';

		$data['scripts'] = $this->document->getScripts('footer');

		return $this->load->view('common/footer_hidden', $data);
	}
}

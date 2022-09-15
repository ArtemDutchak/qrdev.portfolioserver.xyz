<?php
namespace Opencart\Catalog\Controller\Checkout;
class Failure extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('checkout/failure');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['text_message'] = '';
		
		$data['continue'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
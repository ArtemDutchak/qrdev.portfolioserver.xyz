<?php
namespace Opencart\Catalog\Controller\Error;
class NotFound extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('error/not_found');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['continue'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		
		if ($this->customer->isLogged()) {
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		}else{
			$data['footer'] = $this->load->controller('common/footer_hidden');
			$data['header'] = $this->load->controller('common/header_guest');
		}

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$this->response->setOutput($this->load->view('error/not_found', $data));
	}
}
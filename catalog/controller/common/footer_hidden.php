<?php
namespace Opencart\Catalog\Controller\Common;
class FooterHidden extends \Opencart\System\Engine\Controller {
	public function index(): string {
		$this->load->language('common/footer');
				
		$data['bootstrap'] = 'catalog/view/javascript/bootstrap/js/bootstrap.bundle.min.js';

		$data['scripts'] = $this->document->getScripts('footer');

		return $this->load->view('common/footer_hidden', $data);
	}
}

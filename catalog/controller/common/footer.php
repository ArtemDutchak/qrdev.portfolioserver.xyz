<?php
namespace Opencart\Catalog\Controller\Common;
class Footer extends \Opencart\System\Engine\Controller {
	public function index(): string {
		$this->load->language('common/footer');
		
		$data['config_email'] = $this->config->get('config_email');
		$data['config_phone'] = $this->config->get('config_telephone');
		$data['config_digits_phone'] = '+' . preg_replace('/\D/', '', $this->config->get('config_telephone'));
		$data['text_copyright'] = sprintf($this->language->get('text_copyright'), date('Y', time()), $this->config->get('config_name'));
		
		$data['href_msg_telegram'] = 'https://t.me/' . $data['config_digits_phone'];
		$data['href_msg_viber'] = 'viber://chat/?number=' . $data['config_digits_phone'];

		$data['href_social_telegram'] = 'https://telegram.org/';
		$data['href_social_facebook'] = 'https://facebook.com/';
		$data['href_social_twitter'] = 'https://twitter.com/';
				
		$data['bootstrap'] = 'catalog/view/javascript/bootstrap/js/bootstrap.bundle.min.js';

		$data['scripts'] = $this->document->getScripts('footer');

		return $this->load->view('common/footer', $data);
	}
}

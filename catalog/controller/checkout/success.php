<?php
namespace Opencart\Catalog\Controller\Checkout;
class Success extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('checkout/success');
		
		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();
			unset($this->session->data['order_id']);
			unset($this->session->data['payment_address']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		}

		$data['text_message'] = '';
		if (isset($this->session->data['last_order_id'])) {
			$data['text_message'] = sprintf($this->language->get('text_success_order'), $this->session->data['last_order_id']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];


		$data['continue'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
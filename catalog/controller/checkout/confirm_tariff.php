<?php
namespace Opencart\Catalog\Controller\Checkout;
class ConfirmTariff extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('checkout/checkout');

		$json = array(
			'errors' => [],
		);
		
		if (!$this->customer->isLogged()) {
			$json['errors'][] = $this->language->get('error_customer_not_found');
		}

		if (!isset($this->request->post['tariff_id']) || !isset($this->request->post['month'])) {
			$json['errors'][] = $this->language->get('error_input_data');
		}
		
		if ($json['errors']) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		$tariff = [];
		if (!$json['errors']) {
			$this->load->model('account/tariff');
			$tariff = $this->model_account_tariff->getTariff((int)$this->request->post['tariff_id']);
		}

		if (!$tariff) {
			$json['errors'][] = $this->language->get('error_tariff_not_able');
		}
		
		if ($json['errors']) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		$this->load->model('account/customer');
		
		// free tariff
		if (!(int)$tariff['price']) {
			$free_able = $this->model_account_customer->getFreeAble($this->customer->getId());
			if (!$free_able) {
				$json['errors'][] = $this->language->get('error_cannot_activate_free_tariff');
			}
		}
		
		if ($json['errors']) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		if ($tariff) {
			if ((int)$tariff['companies'] < count($this->customer->getActiveCompanyList())) {
				$json['errors'][] = $this->language->get('error_to_many_active_companies');
			}
		}
		
		if ($json['errors']) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		$order_data = [];

		// Store Details
		$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
		$order_data['store_id'] = $this->config->get('config_store_id');
		$order_data['store_name'] = $this->config->get('config_name');
		$order_data['store_url'] = $this->config->get('config_url');

		// Customer Details
		$order_data['customer_id'] = $this->customer->getId();
		$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
		$order_data['firstname'] = $this->session->data['customer']['firstname'];
		$order_data['lastname'] = $this->session->data['customer']['lastname'];
		$order_data['email'] = $this->session->data['customer']['email'];
		$order_data['telephone'] = $this->session->data['customer']['telephone'];
		$order_data['custom_field'] = $this->session->data['customer']['custom_field'];

		// Payment Details
		$order_data['payment_firstname'] = '';
		$order_data['payment_lastname'] = '';
		$order_data['payment_company'] = '';
		$order_data['payment_address_1'] = '';
		$order_data['payment_address_2'] = '';
		$order_data['payment_city'] = '';
		$order_data['payment_postcode'] = '';
		$order_data['payment_zone'] = '';
		$order_data['payment_zone_id'] = 0;
		$order_data['payment_country'] = '';
		$order_data['payment_country_id'] = 0;
		$order_data['payment_address_format'] = '';
		$order_data['payment_custom_field'] = [];

		$order_data['payment_method'] = '';
		$order_data['payment_code'] = '';

		// Shipping Details
		$order_data['shipping_firstname'] = '';
		$order_data['shipping_lastname'] = '';
		$order_data['shipping_company'] = '';
		$order_data['shipping_address_1'] = '';
		$order_data['shipping_address_2'] = '';
		$order_data['shipping_city'] = '';
		$order_data['shipping_postcode'] = '';
		$order_data['shipping_zone'] = '';
		$order_data['shipping_zone_id'] = 0;
		$order_data['shipping_country'] = '';
		$order_data['shipping_country_id'] = 0;
		$order_data['shipping_address_format'] = '';
		$order_data['shipping_custom_field'] = [];

		$order_data['shipping_method'] = '';
		$order_data['shipping_code'] = '';
		
		$quantity = (int)$this->request->post['month'];

		// Products
		$order_data['products'] = [[
			'product_id'   => $tariff['tariff_id'],
			'master_id'    => 0,
			'name'         => $tariff['name'],
			'model'        => '',
			'option'       => [],
			'subscription' => [],
			'download'     => [],
			'quantity'     => $quantity,
			'subtract'     => 0,
			'price'        => (int)$tariff['price'],
			'total'        => $quantity * (int)$tariff['price'],
			'tax'          => 0,
			'reward'       => 0
		]];
		
		// apply specials
		$total = $order_data['products'][0]['total'];
		$special = 0;		
		if ($quantity >= 3 && $quantity < 6) {
			$special = $total * 0.1;
		}elseif ($quantity >= 6 && $quantity < 12) {
			$special = $total * 0.15;
		}elseif ($quantity === 12) {
			$special = $total * 0.2;
		}
		$order_data['products'][0]['total'] = intVal($total - $special);
		
		$order_data['total'] = $order_data['products'][0]['total'];

		// Gift Voucher
		$order_data['vouchers'] = [];
		$order_data['comment'] = '';

		$order_data['affiliate_id'] = 0;
		$order_data['commission'] = 0;
		$order_data['marketing_id'] = 0;
		$order_data['tracking'] = '';

		$order_data['language_id'] = $this->config->get('config_language_id');
		$order_data['language_code'] = $this->config->get('config_language');

		$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
		$order_data['currency_code'] = $this->session->data['currency'];
		$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);

		$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
		} else {
			$order_data['forwarded_ip'] = '';
		}

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
		} else {
			$order_data['user_agent'] = '';
		}

		if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
			$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
		} else {
			$order_data['accept_language'] = '';
		}

		$this->load->model('checkout/order');
		
		$order_id = $this->model_checkout_order->addOrder($order_data);
		if ($order_id) {
			$json['order_id'] = $order_id;
			
			$this->model_checkout_order->addHistory(
				(int)$order_id,
				(int)$this->config->get('config_order_status_id'),
			);
			
			$payment_data = array(
				'order_id' => $order_id,
				'price' => $order_data['total'],
			);
			
			$server_output = $this->load->controller('fondy/tariffs|create_payment', $payment_data);
			if ($server_output['response']['response_status'] === 'success') {
				$json['redirect'] = $server_output['response']['checkout_url'];
			}else{
				$json['errors'][] = $this->language->get('error_payment_service_response');
				$json['response'] = $server_output;
			}
			
			
		}else{
			$json['errors'][] = $this->language->get('error_create_order');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}

	public function confirm(): void {
		$this->response->setOutput($this->index());
	}
}

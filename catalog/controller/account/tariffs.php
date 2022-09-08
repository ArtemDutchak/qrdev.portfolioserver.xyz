<?php
namespace Opencart\Catalog\Controller\Account;
class Tariffs extends \Opencart\System\Engine\Controller {
	public function index(): void {

		if (!$this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/login'));
		}

		$this->load->language('account/tariffs');
		$this->load->model('account/tariff');

		$data['tariffs'] = $this->getTariffs();
		$data['tariff_form_action'] = $this->url->link('checkout/confirm');

		$current_tariff = array();
		if ($this->customer->getCurrentTariffId()) {
			$current_tariff = $this->model_account_tariff->getUserTariff((int)$this->customer->getId());

			foreach ($data['tariffs'] as $tariff) {
				if ($current_tariff['tariff_id'] == $tariff['tariff_id']) {
					$current_tariff['name'] = $tariff['name'];
					$current_tariff['date_to'] = date('d.m.Y', strtotime($current_tariff['active_to']));
				}
			}

		}
		$data['current_tariff'] = $current_tariff;

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('account/tariffs', $data));
	}
	
	public function getTariffs(): array {
		
		$this->load->language('account/tariffs');
		$this->load->model('account/tariff');
		
		$all_tariffs = $this->model_account_tariff->getTariffs();
		
		$tariffs = array();
		
		foreach ($all_tariffs as $tariff) {
			$tariff['options'] = array(
				$this->language->get('option_send_review_with_qr_code'),
				$this->language->get('option_email_notification'),
				$this->language->get('option_ready_qr'),
				$this->language->get('option_1_client_' . $tariff['companies'] . '_company'),
				$this->language->get('option_send_to_google_and_other'),
			);
			$tariffs[] = $tariff;
		}
		
		return $tariffs;
		
	}
	
	public function set(): void {
        $this->db->query('UPDATE oc_customer_tariff SET tariff_id = 2 WHERE customer_id = 29;');


//		$this->load->language('account/tariffs');
//
//		if (!$this->customer->isLogged()) {
//			$this->response->redirect($this->url->link('account/login'));
//		}
//
//		$json = array(
//			'errors' => array(),
//		);
//
//		if ($json['errors']) {
//			$this->response->addHeader('Content-Type: application/json');
//			$this->response->setOutput(json_encode($json));
//			return;
//		}
//
//		$json['msg'] = $this->language->get('success_tariff_activated');
//
//		if ($this->request->post['tariff_id'] == 1) {
//			$this->request->post['month'] = 1;
//		}
//
//		$date_now  = date('Y-m-d H:i:s', time());
//
//		$exist_tariff = $this->customer->getCurrentTariffInfo();
//		if ($exist_tariff && (int)$exist_tariff['tariff_id'] === (int)$this->request->post['tariff_id']) {
//			$date_activated = $exist_tariff['date_activated'];
//		}else{
//			$date_activated = $date_now;
//		}
//
//		$date_to = date('Y-m-d', strtotime("+" . $this->request->post['month'] . " months", strtotime($date_now)));
//		$date_to = date('Y-m-d', strtotime("+ 1 day", strtotime($date_to)));
//		$date_to = $date_to . ' 23:59:59';
//
//		$tariff_data = array(
//			'customer_id'    => $this->customer->getId(),
//			'tariff_id'      => $this->request->post['tariff_id'],
//			'active_to'      => $date_to,
//			'date_activated' => $date_activated
//		);
//
//		$this->load->model('account/tariff');
//
//		if ($this->customer->getCurrentTariffId()) {
//			$this->model_account_tariff->editTariff($tariff_data);
//		}else{
//			$this->model_account_tariff->addTariff($tariff_data);
//		}
//
//		$this->response->addHeader('Content-Type: application/json');
//		$this->response->setOutput(json_encode($json));
		
	}
}
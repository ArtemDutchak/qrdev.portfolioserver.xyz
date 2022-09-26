<?php
namespace Opencart\Admin\Controller\Cron;
class TariffNotification extends \Opencart\System\Engine\Controller {
	public function index(int $cron_id, string $code, string $cycle, string $date_added, string $date_modified): void {
		$this->load->model('customer/customer');
		
		$results_3_days = $this->model_customer_customer->getByTariffEnds(3);
		$this->load->controller('mail/tariff|remind_3_days', $results_3_days);
		
		$results_expired = $this->model_customer_customer->getByTariffEnds(-1);
		$this->load->controller('mail/tariff|remind_expired', $results_expired);

	}
}

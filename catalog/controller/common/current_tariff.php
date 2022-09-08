<?php
namespace Opencart\Catalog\Controller\Common;
class CurrentTariff extends \Opencart\System\Engine\Controller {
	public function index(): string {
		
		$data['current_tariff'] = $this->customer->getCurrentTariffInfo();
		
		if ($data['current_tariff']) {
			$data['current_tariff']['active_to'] = date('d.m.Y', strtotime($data['current_tariff']['active_to']));
		}else{
			$data['current_tariff'] = array(
				'name' => $this->language->get('text_current_tariff_none'),
				'active_to' => false,
			);
		}

		return $this->load->view('common/current_tariff', $data);
	}
	
}
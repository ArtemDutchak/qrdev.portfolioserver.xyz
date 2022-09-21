<?php
namespace Opencart\Catalog\Controller\Mail;
class Company extends \Opencart\System\Engine\Controller {
	// catalog/model/account/company/addCompany/after
	public function index(string &$route, array &$args, mixed &$output): void {
		if (in_array('company', (array)$this->config->get('config_mail_alert'))) {
			
			$this->load->language('mail/company');
			
			$customer_id = (int)$args[0]['customer_id'];
			$company_id = (int)$output;
			if (!$company_id && !$customer_id) {
				return;
			}
			
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->getCustomer($customer_id);
			if (!$customer_info) {
				return;
			}
			
			$data['customer_name'] = $customer_info['firstname'];
			$data['company_name']  = $args[0]['company_name'];
			$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
			$subject = sprintf($this->language->get('text_subject'), $store_name);

			if ($this->config->get('config_mail_engine')) {
				$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($this->config->get('config_email'));
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($store_name);
				$mail->setSubject($subject);
				$mail->setHtml($this->load->view('mail/company', $data));
				$mail->send();

				// Send to additional alert emails
				$emails = explode(',', (string)$this->config->get('config_mail_alert_email'));

				foreach ($emails as $email) {
					if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$mail->setTo(trim($email));
						$mail->send();
					}
				}
			}
			
		}
	}
}

<?php
namespace Opencart\Admin\Controller\Mail;
class Tariff extends \Opencart\System\Engine\Controller {
	
	public function remind_expired(array $customers): void {
		
		if (!$customers) {
			return;
		}

		$this->language->load('mail/customer','','uk-ua');
		$this->load->model('setting/store');
		
		$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$store_url = $this->config->get('config_url');

		$subject = sprintf($this->language->get('mail_text_subject_tariff_reminder'), $store_name);
		$data['store'] = $store_name;
		$data['store_url'] = $store_url;

		if ($this->config->get('config_mail_engine')) {
			
			$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			
			$data['text_reminder'] = $this->language->get('text_expired_tariff_reminder');
			
			foreach ($customers as $customer) {
				
				$data['customer'] = $customer;
				
				$mail->setTo($customer['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($store_name);
				$mail->setSubject($subject);
				$mail->setHtml($this->load->view('mail/tariff_reminder', $data));
				$mail->send();
				
			}
			
		}
		
	}
		
	public function remind_3_days(array $customers): void {
		
		if (!$customers) {
			return;
		}

		$this->language->load('mail/customer','','uk-ua');
		$this->load->model('setting/store');
		
		$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$store_url = $this->config->get('config_url');

		$subject = sprintf($this->language->get('mail_text_subject_tariff_reminder'), $store_name);
		$data['store'] = $store_name;
		$data['store_url'] = $store_url;

		if ($this->config->get('config_mail_engine')) {
			
			$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			
			$data['text_reminder'] = $this->language->get('text_3_days_tariff_reminder');
			
			foreach ($customers as $customer) {
				
				$data['customer'] = $customer;
				
				$mail->setTo($customer['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($store_name);
				$mail->setSubject($subject);
				$mail->setHtml($this->load->view('mail/tariff_reminder', $data));
				$mail->send();
				
			}
			
		}
		
	}
		
}	

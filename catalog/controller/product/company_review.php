<?php
namespace Opencart\Catalog\Controller\Product;
use \Opencart\System\Helper as Helper;
class CompanyReview extends \Opencart\System\Engine\Controller {
	public function index(): void {
		
		if (!$this->customer->isLogged()) {
			$this->config->set('config_language', $this->config->get('config_language'));
		}
		
		$this->load->language('product/review');

		if (isset($this->request->get['company_code'])) {
			$company_code = $this->request->get['company_code'];
		} else {
			$company_code = 0;
		}
		
		if (!$company_code) {
			$this->response->redirect($this->url->link('error/not_found'));
		}
		
		$this->load->model('catalog/company');
		$company_info = $this->model_catalog_company->getCompanyByCode($company_code);
		
		if (!$company_info) {
			$this->response->redirect($this->url->link('error/not_found'));
		}
		
		$company_settings = json_decode($company_info['settings'], true);

		if ($company_settings['custom_field_1']['active']) {
			$data['text_field_1'] = $company_settings['custom_field_1']['value'];
		}else{
			$data['text_field_1'] = '';
		}

		// Create a login token to prevent brute force attacks
		$this->session->data['review_token'] = Helper\General\token(32);

		$data['review_token'] = $this->session->data['review_token'];
		
		$this->load->model('tool/image');
		if ($company_info['image'] && is_file(DIR_IMAGE . html_entity_decode($company_info['image'], ENT_QUOTES, 'UTF-8'))) {
			$thumb = $this->model_tool_image->resize(html_entity_decode($company_info['image'], ENT_QUOTES, 'UTF-8'), 140, 140);
		} else {
			$thumb = '';
		}
		
		$data['company'] = array(
			'name'   => $company_info['company_name'],
			'image'  => $thumb,
			'status' => $company_info['status'],
		);

		// Captcha
		$this->load->model('setting/extension');

		$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

		if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/'  . $extension_info['extension'] . '/captcha/' . $extension_info['code']);
		} else {
			$data['captcha'] = '';
		}

		$data['language'] = $this->config->get('config_language');
		
		$data['form_action'] = $this->url->link('product/company_review|write', 'company_code=' . $company_code . '&review_token=' . $data['review_token']);
				
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer_hidden');
		$data['header'] = $this->load->controller('common/header_hidden');

		$this->response->setOutput($this->load->view('product/company_review', $data));
	}

	public function write(): void {
		
		if (!$this->customer->isLogged()) {
			$this->config->set('config_language', $this->config->get('config_language'));
		}
		
		$this->load->language('product/review');

		$json = [
			'errors' => [],
		];

		if (!isset($this->request->get['company_code'])) {
			$json['errors'][] = [
				'field_name' => '',
				'text' => $this->language->get('error_company_not_found'),
			];
		}
		
		if ($json['errors']) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		$company_code = $this->request->get['company_code'];
		
		$this->load->model('catalog/company');
		
		$company_info = $this->model_catalog_company->getCompanyByCode($company_code);

		if (!$company_info) {
			$json['errors'][] = [
				'field_name' => '',
				'text' => $this->language->get('error_company_not_found'),
			];
		}
		
		if ($json['errors']) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		if (!isset($this->request->get['review_token']) || !isset($this->session->data['review_token']) || $this->request->get['review_token'] != $this->session->data['review_token']) {
			$json['errors'][] = [
				'field_name' => '',
				'text' => $this->language->get('error_init_client'),
			];
		}

		$keys = [
			'name',
			'text',
			'email',
			'telephone',
			'stars'
		];

		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}
		
		$name  = trim($this->request->post['name']);
		$email = trim($this->request->post['email']);
		$telephone = preg_replace('/\D/', '', trim($this->request->post['telephone']));
		$text  = trim($this->request->post['text']);
		$stars = (int)$this->request->post['stars'];
		
		$this->request->post['email']        = $email;
		$this->request->post['name']         = $name;
		$this->request->post['telephone']    = $telephone;
		$this->request->post['text']         = $text;
		$this->request->post['rating']       = $stars;
		$this->request->post['date_added']   = date('Y-m-d H:i:s', time());
		
		
		$telephone_is_required = false;
		$settings = json_decode($company_info['settings'], true);
		if ($settings['customer_telephone_required']['active'] && $stars <= (int)$settings['customer_telephone_required']['rating']) {
			$telephone_is_required = true;
		}

		if ((Helper\Utf8\strlen($name) < 1) || (Helper\Utf8\strlen($name) > 25)) {
			$json['errors'][] = [
				'field_name' => 'name',
				'text' => $this->language->get('error_name'),
			];
		}

		if ((Helper\Utf8\strlen($text) < 1) || (Helper\Utf8\strlen($text) > 1000)) {
			$json['errors'][] = [
				'field_name' => 'text',
				'text' => $this->language->get('error_text'),
			];
		}
		
		if (Helper\Utf8\strlen($this->request->post['text']) < 1) {
			$json['errors'][] = [
				'field_name' => 'text',
				'text' => $this->language->get('error_text'),
			];
		}elseif (Helper\Utf8\strlen($this->request->post['text']) > 1000) {
			$json['errors'][] = [
				'field_name' => 'text',
				'text' => $this->language->get('error_text_length'),
			];
		}

		if ($stars < 1 || $stars > 5) {
			$json['errors'][] = [
				'field_name' => 'stars',
				'text' => $this->language->get('error_rating'),
			];
		}
		
		// if ((Helper\Utf8\strlen($email) > 96) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// 	$json['error']['email'] = $this->language->get('error_email');
		// 	$json['errors'][] = array(
		// 		'field_name' => 'email',
		// 		'text' => $this->language->get('error_email'),
		// 	);
		// }
		
		if ($telephone_is_required && (Helper\Utf8\strlen($telephone) != 12)) {
			$json['error']['telephone'] = $this->language->get('error_telephone');
			$json['errors'][] = array(
				'field_name' => 'telephone',
				'text' => $this->language->get('error_telephone'),
			);
		}

		// Captcha
		$this->load->model('setting/extension');

		$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

		if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/'  . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '|validate');

			if ($captcha) {
				$json['error']['captcha'] = $captcha;
			}
		}

		if ($json['errors']) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		
		$this->load->model('catalog/review');

		$company_id = (int)$company_info['company_id'];
		$review_id = $this->model_catalog_review->addCompanyReview($company_id, $this->request->post);

		if ($review_id) {
			
			// send telegram notification to company owner
			if ($settings['review_notification']['telegram']['active'] && $settings['review_notification']['telegram']['value']) {
	            $telegramId = $settings['review_notification']['telegram']['value'];

	            $data = [
	                'chat_id' => $telegramId,
	                'text' => 'У вас новий відгук по компанії "' . $company_info['company_name'] .'"'
	            ];

	            $this->sendTelegramNotification($data);
			}
			
			// send email notification to company owner
			if ($settings['review_notification']['email']['active'] && $settings['review_notification']['email']['value']) {
				$client_email_data = [
					'email'   => $settings['review_notification']['email']['value'],
					'subject' => $this->language->get('text_new_review'),
					'text'    => sprintf($this->language->get('text_new_review_written'), $company_info['company_name']),
				];
				$this->sendEmailNotification($client_email_data);
			}

			$this->session->data['company_code'] = $company_code;
			$json['redirect'] = $this->url->link('product/company_review|success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    public function non_active(){
		
		$data['111'] = '';
		$this->response->setOutput($this->load->view('product/non_active_company', $data));
		
	}
	
    public function sendTelegramNotification(array $data, $headers = [])
    {
        $method = 'sendMessage';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/' . $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
        ]);

        $result = curl_exec($curl);
        curl_close($curl);
        return (json_decode($result, 1) ? json_decode($result, 1) : $result);
    }

    public function sendEmailNotification(array $data) :void {
        
		if ($this->config->get('config_mail_engine')) {
			$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($data['email']);
			// Less spam and fix bug when using SMTP like sendgrid.
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($data['subject']));
			$mail->setText(html_entity_decode($data['text']));
			$mail->send();
		}
		
    }

	public function success(): void {
		
		if (!$this->customer->isLogged()) {
			$this->config->set('config_language', $this->config->get('config_language'));
		}
		
		$this->load->language('product/review');
		
		if (isset($this->session->data['company_code'])) {
			$company_code = $this->session->data['company_code'];
		} else {
			$company_code = 0;
		}
		
		if (!$company_code) {
			$this->response->redirect($this->url->link('error/not_found'));
		}
		
		$this->load->model('catalog/company');
		$company_info = $this->model_catalog_company->getCompanyByCode($company_code);
		
		if (!$company_info) {
			$this->response->redirect($this->url->link('error/not_found'));
		}
		
		$this->load->model('tool/image');
		if (is_file(DIR_IMAGE . html_entity_decode($company_info['image'], ENT_QUOTES, 'UTF-8'))) {
			$thumb = $this->model_tool_image->resize(html_entity_decode($company_info['image'], ENT_QUOTES, 'UTF-8'), 140, 140);
		} else {
			$thumb = '';
		}
		
		$data['company'] = array(
			'name' => $company_info['company_name'],
			'image' => $thumb,
		);
		
		$data['footer'] = $this->load->controller('common/footer_hidden');
		$data['header'] = $this->load->controller('common/header_hidden');

		$this->response->setOutput($this->load->view('product/review_success', $data));
	}

	public function list(): void {
		$this->load->language('product/review');

		$this->response->setOutput($this->getList());
	}

	public function getList(): string {
		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = [];

		$this->load->model('catalog/review');

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($product_id);

		$results = $this->model_catalog_review->getReviewsByProductId($product_id, ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = [
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $review_total,
			'page'  => $page,
			'limit' => 5,
			'url'   => $this->url->link('product/review|list', 'language=' . $this->config->get('config_language') . '&product_id=' . $product_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		return $this->load->view('product/review_list', $data);
	}
}

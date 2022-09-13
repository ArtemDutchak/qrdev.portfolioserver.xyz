<?php

namespace Opencart\Catalog\Controller\Account;

use QRcode;

require $_SERVER['DOCUMENT_ROOT'] . '/storage/vendor/phpqrcode/qrlib.php';

class Company extends \Opencart\System\Engine\Controller
{

    public function list(): void
    {
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/login'));
        }

        $this->load->language('company/list');
        $this->load->model('tool/image');

        $data['href_company_add'] = $this->url->link('account/company|form');

        $companies = $this->customer->getCompanyList();

        $data['companies'] = array();

        foreach ($companies as $result) {

            if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
                $thumb = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), 120, 120);
            } else {
                $thumb = '';
            }

            $rating = 4;

            $qr_link = $this->url->link('product/company_review', 'company_code=' . $result['company_code']);

            $company = array(
                'id' => $result['company_id'],
                'name' => $result['company_name'],
                'thumb' => $thumb,
                'qr_link' => $qr_link,
                'code' => $result['company_code'],
//				'google_qr_link' => $this->getQr(str_replace("amp;", "",$qr_link)),
                'qr_thumb' => '/catalog/view/stylesheet/static/img/qr-code.png',
                'rating_width' => ($rating / 5) * 100,
                'rating' => $rating,
                'href_edit' => $this->url->link('account/company|form', 'company_id=' . $result['company_id']),
                'href_generate' => $this->url->link('account/company|form', 'company_id=' . $result['company_id']),
                'href_download_qr' => $this->url->link('account/company|form', 'company_id=' . $result['company_id']),
            );

            $data['companies'][] = $company;

        }

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('account/company_list', $data));

    }

    public function form(): void
    {

        $this->load->language('account/company');

        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/login'));
        }

        if (isset($this->request->get['company_id'])) {

            $company_info = $this->customer->getCompanyInfo((int)$this->request->get['company_id']);

            if (!$company_info) {
                $data['breadcrumbs'] = array();
                $this->document->setTitle($this->language->get('text_error'));
                $data['continue'] = $this->url->link('common/home');
                $data['header'] = $this->load->controller('common/header');
                $data['footer'] = $this->load->controller('common/footer');
                $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
                $this->response->setOutput($this->load->view('error/not_found', $data));
                return;
            }

            $data['company_form_action'] = $this->url->link('account/company|edit', 'company_id=' . $this->request->get['company_id']);
            $data['title_company_form'] = $this->language->get('title_company_edit');

        } else {

            $data['company_form_action'] = $this->url->link('account/company|edit');
            $data['title_company_form'] = $this->language->get('title_company_add');

        }

        if (!empty($company_info)) {
            $data['company_name'] = $company_info['company_name'];
            $data['status'] = $company_info['status'];
        } else {
            $data['company_name'] = '';
            $data['status'] = true;
        }

        $this->load->model('tool/image');
        if (is_file(DIR_IMAGE . html_entity_decode($company_info['image'], ENT_QUOTES, 'UTF-8'))) {
            $data['company_image'] = $this->model_tool_image->resize(html_entity_decode($company_info['image'], ENT_QUOTES, 'UTF-8'), 185, 185);
        } else {
            $data['company_image'] = '';
        }

        if (!empty($company_info)) {
            $settings = json_decode($company_info['settings'], true);
        } else {
            $settings = $this->get_empty_settings();
        }

        $data['settings'] = $settings;

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('account/company_form', $data));

    }

    public function edit(): void
    {


        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/login'));
        }

        $this->load->model('account/company');

        $json = array(
            'errors' => array(),
        );

        if (!empty($this->request->files) && !empty($this->request->post['code'])) {

            $path_parts = pathinfo($_FILES["file"]["name"]);
            $extension = $path_parts['extension'];

            $filename = $this->request->post['code'] . '.' . $extension;

            $result = $this->save_company_logo($filename);

            $this->model_account_company->addCompanyImage($this->request->post['code'], 'company_logo/' . $filename);

            $json = json_encode($result);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->language('account/company');

        if (empty($this->request->post['company_name'])) {
            $json['errors'][] = array(
                'field_name' => 'company_name',
                'text' => $this->language->get('error_company_name'),
            );
        }

        if ($json['errors']) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $company = array(
            'customer_id' => $this->customer->getId(),
            'company_name' => $this->request->post['company_name'],
            'image' => '',
            'status' => false,
            'settings' => $this->get_empty_settings(),
        );

        if (isset($this->request->post['status']) && $this->request->post['status'] == 'on') {
            $company['status'] = true;
        }
        
        if (isset($this->request->post['check_telephone_required']) && $this->request->post['check_telephone_required'] == 'on') {
            $company['settings']['customer_telephone_required']['active'] = true;
        } else {
            $company['settings']['customer_telephone_required']['active'] = false;
        }
        $company['settings']['customer_telephone_required']['rating'] = $this->request->post['telephone_required_rating'];

        if (isset($this->request->post['check_duplicate_review']) && $this->request->post['check_duplicate_review'] == 'on') {
            $company['settings']['review_dublication']['active'] = true;
        } else {
            $company['settings']['review_dublication']['active'] = false;
        }
        $company['settings']['review_dublication']['rating'] = $this->request->post['review_dublication_rating'];

        if (isset($this->request->post['check_notification_telegram_phone']) && $this->request->post['check_notification_telegram_phone'] == 'on') {
            $company['settings']['review_notification']['telegram']['active'] = true;
        } else {
            $company['settings']['review_notification']['telegram']['active'] = false;
        }
        $company['settings']['review_notification']['telegram']['value'] = $this->request->post['notification_telegram_phone'];

        if (isset($this->request->post['check_notification_email']) && $this->request->post['check_notification_email'] == 'on') {
            $company['settings']['review_notification']['email']['active'] = true;
        } else {
            $company['settings']['review_notification']['email']['active'] = false;
        }
        $company['settings']['review_notification']['email']['value'] = $this->request->post['notification_email'];

        if (isset($this->request->post['check_duplicate_google']) && $this->request->post['check_duplicate_google'] == 'on') {
            $company['settings']['review_dublication']['services']['google']['active'] = true;
        } else {
            $company['settings']['review_dublication']['services']['google']['active'] = false;
        }
        $company['settings']['review_dublication']['services']['google']['value'] = $this->request->post['duplicate_google'];

        if (isset($this->request->post['check_duplicate_facebook']) && $this->request->post['check_duplicate_facebook'] == 'on') {
            $company['settings']['review_dublication']['services']['facebook']['active'] = true;
        } else {
            $company['settings']['review_dublication']['services']['facebook']['active'] = false;
        }
        $company['settings']['review_dublication']['services']['facebook']['value'] = $this->request->post['duplicate_facebook'];

        if (isset($this->request->post['check_duplicate_profile_link']) && $this->request->post['check_duplicate_profile_link'] == 'on') {
            $company['settings']['review_dublication']['services']['custom']['active'] = true;
        } else {
            $company['settings']['review_dublication']['services']['custom']['active'] = false;
        }
        $company['settings']['review_dublication']['services']['custom']['name'] = $this->request->post['duplicate_profile_name'];
        $company['settings']['review_dublication']['services']['custom']['link'] = $this->request->post['duplicate_profile_link'];

        $custom_field = array(
            'active' => false,
            'value' => '',
        );

        if (isset($this->request->post['check_text_field_1']) && $this->request->post['check_text_field_1'] == 'on') {
            $custom_field['active'] = true;
        }

        $custom_field['value'] = $this->db->escape($this->request->post['text_field_1']);

        $company['settings']['custom_field_1'] = $custom_field;
        
        $custom_field = array(
            'active' => false,
            'value' => '',
        );

        if (isset($this->request->post['check_text_field_2']) && $this->request->post['check_text_field_2'] == 'on') {
            $custom_field['active'] = true;
        }

        $custom_field['value'] = $this->db->escape($this->request->post['text_field_2']);

        $company['settings']['custom_field_2'] = $custom_field;

        if (isset($this->request->get['company_id'])) {

            $company_info = $this->customer->getCompanyInfo((int)$this->request->get['company_id']);

            $json['company_code'] = $company_info['company_code'];
            $this->generateQrCode($json['company_code']);

            $json['msg'] = $this->language->get('text_success_edit');

            $company['company_id'] = $this->request->get['company_id'];
            $this->model_account_company->editCompany($company);

        } else {

            $this->load->model('account/tariff');
            $current_tariff_id = $this->customer->getCurrentTariffId();
            $current_tariff_info = $this->model_account_tariff->getTariff((int)$current_tariff_id);

            if ((int)$current_tariff_info['companies'] > count($this->customer->getCompanyList())) {

                $company_id = $this->model_account_company->addCompany($company);
                if ($company_id) {
                    $company_info = $this->customer->getCompanyInfo($company_id);

                    $json['company_code'] = $company_info['company_code'];
                    $this->generateQrCode($json['company_code']);

                    $json['msg'] = $this->language->get('text_success_added');
                } else {
                    $json['msg'] = $this->language->get('error_add_company');
                }

            } else {

                $json['msg'] = $this->language->get('error_add_cause_many_companies');

            }
        }



        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    public function generateQrCode(string $companyCode): void
    {
        $codeContents = HTTP_SERVER . 'index.php?route=product/company_review&company_code=' . $companyCode;

        $fileName = $companyCode . '.png';
        $tempDir = 'storage/qr_codes/';

        $pngAbsoluteFilePath = $tempDir . $fileName;

        if (!file_exists($pngAbsoluteFilePath)) {
            QRcode::png($codeContents, $pngAbsoluteFilePath);
        }
    }

    public function get_empty_settings(): array
    {

        return array(
            'review_notification' => array(
                'telegram' => array(
                    'active' => false,
                    'value' => '',
                ),
                'email' => array(
                    'active' => false,
                    'value' => '',
                ),
            ),
            'customer_telephone_required' => array(
                'active' => false,
                'rating' => 1,
            ),
            'review_dublication' => array(
                'active' => false,
                'rating' => 1,
                'services' => array(
                    'google' => array(
                        'active' => false,
                        'value' => '',
                    ),
                    'facebook' => array(
                        'active' => false,
                        'value' => '',
                    ),
                    'custom' => array(
                        'active' => false,
                        'name' => '',
                        'link' => '',
                    )
                ),
            ),
            'custom_field_1' => array(
                'active' => false,
                'value' => '',
            ),
            'custom_field_2' => array(
                'active' => false,
                'value' => '',
            ),
        );

    }

    private function save_company_logo(string $filename): array
    {
        $this->load->language('tool/upload');

        $json = [];

        if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {

            // Allowed file extension types
            $allowed = [];

            $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

            $filetypes = explode("\n", $mime_allowed);

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array($this->request->files['file']['type'], $allowed)) {
                $json['error'] = $this->language->get('error_file_type');
            }

            // Return any upload error
            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
            }
        } else {
            $json['error'] = $this->language->get('error_upload');
        }

        if (!$json) {

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_IMAGE . 'company_logo/' . $filename);

            $json['success'] = $this->language->get('text_upload');
        }

        return $json;
    }

}

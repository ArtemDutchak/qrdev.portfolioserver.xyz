<?php

namespace Opencart\Catalog\Controller\Fondy;

class Response extends \Opencart\System\Engine\Controller {
    public function index()
    {
        
        print_r($this->session->data);
        return;

        if (!isset($this->request->get['o']) || !isset($this->request->get['c'])) {
            $this->response->redirect($this->url->link('error/not_found'));
        }
        
        $order_id = $this->request->get['o'];
        $customer_code = $this->request->get['c'];
        
        $this->load->model('account/customer');
        $customer = $this->model_account_customer->getCustomerByActivationCode($customer_code);
        
        $query = $this->db->query(
            "SELECT order_status_id
            FROM `" . DB_PREFIX . "order`
            WHERE order_id = '" . $order_id . "'
            AND customer_id = '" . $customer['customer_id'] . "'
            "
        );
        
        if ($query->row && $this->customer->login($customer['email'], '', true)) {
            $this->response->redirect($this->url->link('checkout/success'));
        }else{
            $this->response->redirect($this->url->link('error/not_found'));
        }
        

    }
}
<?php

namespace Opencart\Catalog\Controller\Fondy;

class Tariffs extends \Opencart\System\Engine\Controller
{
    public function pay()
    {
        $data = json_decode($_POST['json']);
        $tariffId = $data->tariff_id;
        $month = $data->month;

        //
//        if (!$this->customer->isLogged()) {
//            $this->response->redirect($this->url->link('account/login'));
//        }
//
//        $json = array(
//            'errors' => array(),
//        );

//        if ($json['errors']) {
//            $this->response->addHeader('Content-Type: application/json');
//            $this->response->setOutput(json_encode($json));
//            return;
//        }

        $tariff = $this->db->query("SELECT * FROM oc_tariff WHERE tariff_id = {$tariffId};");
        $price = ($tariff->row['price'] * $month) * 100;

        $data = [
            "order_id" => "test" . rand(100,1000000),
            'order_desc' => 'Test',
            'currency' => 'UAH',
            'amount' => $price,
            'server_callback_url' => HTTP_SERVER . 'index.php?route=fondy/callback',
            'response_url' => HTTP_SERVER . 'index.php?route=fondy/response'
        ];

        $signature = $this->getSignature(MERCHANT_ID, PAYMENT_KEY, $data);

        $data['signature'] = $signature;
        $data['merchant_id'] = MERCHANT_ID;

        $data = json_encode(array("request" => $data));

        $ch = curl_init("https://pay.fondy.eu/api/checkout/url/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = json_decode(curl_exec($ch));

        curl_close($ch);

        echo json_encode($server_output);
    }
    public function create_payment(array $payment_data) :array
    {
        $this->load->model('account/customer');
        $customer = $this->model_account_customer->getCustomer($this->customer->getId());
        
        $data = [
            "order_id" => $payment_data['order_id'],
            'order_desc' => 'Test',
            'currency' => 'UAH',
            'amount' => (int)$payment_data['price'] * 100,
            'server_callback_url' => HTTP_SERVER . 'index.php?route=fondy/callback',
            // 'response_url' => HTTP_SERVER . 'index.php?route=fondy/response&o=' . $payment_data['order_id'] . '&c=' . $customer['customer_code'] . ''
        ];

        $signature = $this->getSignature(MERCHANT_ID, PAYMENT_KEY, $data);

        $data['signature'] = $signature;
        $data['merchant_id'] = MERCHANT_ID;

        $data = json_encode(array("request" => $data));

        $ch = curl_init("https://pay.fondy.eu/api/checkout/url/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = json_decode(curl_exec($ch), true);

        curl_close($ch);

        return $server_output;
    }

    public function getSignature($merchant_id, $password, $params = array())
    {
        $params['merchant_id'] = $merchant_id;
        $params = array_filter($params, 'strlen');
        ksort($params);
        $params = array_values($params);
        array_unshift($params, $password);
        $params = join('|', $params);
        return (sha1($params));
    }
}
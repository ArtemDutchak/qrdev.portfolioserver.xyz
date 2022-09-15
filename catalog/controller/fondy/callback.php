<?php

namespace Opencart\Catalog\Controller\Fondy;

class Callback extends \Opencart\System\Engine\Controller {
    public function index()
    {
        $callback = file_get_contents('php://input');
        $callaback_object = json_decode($callback);
        
        if (!$callaback_object) {
            $this->response->redirect($this->url->link('error/not_found'));
        }
        
        $this->load->model('checkout/order');
        
        $order_id = (int)$callaback_object->order_id;
        $order_id = (int)$callaback_object->order_id;
        $order_info = $this->model_checkout_order->getOrder($order_id);
        
        $customer_id = (int)$order_info['customer_id'];
        

        if ($callaback_object->order_status == 'approved') {
            
            $order_products = $this->model_checkout_order->getProducts($order_id);
            
            $this->load->model('account/tariff');
            $current_tariff_info = $this->model_account_tariff->getUserTariff($customer_id);
            
            if ($current_tariff_info) {
                
                if ((int)$order_products[0]['product_id'] == $current_tariff_info['tariff_id']) {
                    
                    $active_to = $current_tariff_info['active_to'];
                    $new_active_to = date('Y-m-d', strtotime("+" . $order_products[0]['quantity'] . " months", strtotime($active_to)));
            		$new_active_to = $new_active_to . ' 23:59:59';
                    
                    $new_tariff_data = array(
                        'tariff_id' => $current_tariff_info['tariff_id'],
                        'active_to' => $new_active_to,
                        'date_activated' => $current_tariff_info['date_activated'],
                        'customer_id' => $customer_id,
                    );
                    
                }else{
                    
                    $date_now  = date('Y-m-d H:i:s', time());
                    $date_to = date('Y-m-d', strtotime("+" . $order_products[0]['quantity'] . " months", strtotime($date_now)));
            		$date_to = date('Y-m-d', strtotime("+ 1 day", strtotime($date_to)));
            		$date_to = $date_to . ' 23:59:59';
                
                    $new_tariff_data = array(
                        'tariff_id' => $order_products[0]['product_id'],
                        'active_to' => $date_to,
                        'date_activated' => $date_now,
                        'customer_id' => $customer_id,
                    );
                    
                }
                
                $this->model_account_tariff->editTariff($new_tariff_data);
                
            }else{
                
                $date_now  = date('Y-m-d H:i:s', time());
                $date_to = date('Y-m-d', strtotime("+" . $order_products[0]['quantity'] . " months", strtotime($date_now)));
                $date_to = date('Y-m-d', strtotime("+ 1 day", strtotime($date_to)));
                $date_to = $date_to . ' 23:59:59';
            
                $new_tariff_data = array(
                    'tariff_id' => $order_products[0]['product_id'],
                    'active_to' => $date_to,
                    'date_activated' => $date_now,
                    'customer_id' => $customer_id,
                );
                
                $this->model_account_tariff->addTariff($new_tariff_data);
                
            }
            
            $config_complete_status = 5;
            
            $this->model_checkout_order->addHistory(
				(int)$order_id,
				(int)$config_complete_status,
			);
            
            $amount = (float)$callaback_object->actual_amount / 100;
            
            $this->load->model('account/transaction');
            
            $transaction_description = "OrderId: "  . $order_id . ", PaymentId: " . $callaback_object->payment_id;
            
            $this->model_account_transaction->addTransaction($customer_id, $transaction_description, $amount, $order_id);
            
        }else{
            
    		$config_fail_status = 10;
            
            $this->model_checkout_order->addHistory(
				(int)$order_id,
				(int)$config_fail_status,
			);
            
        }
    }
}
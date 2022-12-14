<?php
namespace Opencart\Catalog\Controller\Extension\Feedback\Startup;

class Feedback extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        
        if ($this->config->get('theme_feedback_status')) {
            
            $this->event->register('view/*/before', new \Opencart\System\Engine\Action('extension/feedback/startup/feedback|event'));
        }
    }

    public function event(string &$route, array &$args, mixed &$output): void
    {
        $override = [
            
            'common/header',
            'common/header_hidden',
            'common/header_guest',
            'common/footer',
            'common/footer_hidden',
            'common/success',
            'common/account_button',
            'common/current_tariff',
            'common/login_button',
            'common/pagination',
            
            'account/login',
            'account/register',
            'account/register_success',
            'account/activation_success',
            'account/forgotten_reset',
            'account/edit',
            'account/company',
            'account/company_form',
            'account/company_list',
            'account/tariffs',
            'account/reviews',
            'account/tariff_expired_block',
            
            'ajax/language',
            
            'information/contacts',
            
            'mail/register',
            
            'product/company_review',
            'product/review_success',
            'product/review_dublicate',
            
        ];

        if (in_array($route, $override)) {
            $route = 'extension/feedback/' . $route;
        }
    }
}

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
            'common/footer',
            'common/success',
            'common/account_button',
            'common/current_tariff',
            'common/login_button',
            'common/pagination',
            
            'account/login',
            'account/register',
            'account/register_success',
            'account/activation_success',
            'account/edit',
            'account/company',
            'account/company_form',
            'account/company_list',
            'account/tariffs',
            'account/reviews',
            
            'ajax/language',
            
            'information/contacts',
            
            'mail/register',
            
        ];

        if (in_array($route, $override)) {
            $route = 'extension/feedback/' . $route;
        }
    }
}

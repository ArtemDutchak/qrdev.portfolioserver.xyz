<?php
namespace Opencart\Admin\Catalog\Controller\Extension\Feedback\Startup;

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
            
            'mail/tariff_reminder',
            
        ];

        if (in_array($route, $override)) {
            $route = 'extension/feedback/' . $route;
        }
    }
}

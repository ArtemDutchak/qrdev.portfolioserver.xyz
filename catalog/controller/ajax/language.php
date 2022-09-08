<?php
namespace Opencart\Catalog\Controller\Ajax;

class Language extends \Opencart\System\Engine\Controller {
	
	private $available_langs = array();
	
	public function get_data() {
		
		$data = array(
			'code'     => 'uk-ua',
			'redirect' => false,
		);
		
		$code = $data['code'];
		
		if (isset($_COOKIE['language'])) {
			$code = $this->request->cookie['language'];
		}
		
		$end_lang_data = $this->get_end_data();
		$route_data = $this->get_route_data();
				
		$data['code'] = $end_lang_data['code'];
		
		if ($route_data['is_seo']) {
			
			if ($route_data['prefix'] != $end_lang_data['prefix']) {
				
				if ($end_lang_data['prefix']) {
					array_unshift($route_data['parts'], $end_lang_data['prefix']);
				}

				$data['redirect'] = '/' . implode('/', $route_data['parts']);
				
			}
			
		}else {
			$data['redirect'] = false;
		}
		
		
		if ($this->request->post) {
			$data['redirect'] = false;
		}
		
		return $data;
		
	}
	
	private function get_route_data(){
		
		$data = array(
			'prefix' => '',
			'route'  => '',
			'url'  => $_SERVER['REQUEST_URI'],
			'is_seo'  => true,
			'parts'  => array(),
		);
		
		if (isset($this->request->get['_route_'])) {
			
			$parts = explode('/', $this->request->get['_route_']);
			
			foreach ($parts as $part) {
				if (strpos($part, 'index.php') !== false) {
					$data['is_seo'] = false;
				}
			}
			
			$prefix_founded = false;
			foreach ($this->available_langs as $lang) {
				if ($parts[0] == $lang['prefix']) {
					$prefix_founded = true;
					$data['prefix'] = $lang['prefix'];
				}
			}
			
			if ($prefix_founded) {
				array_shift($parts);
			}
			
			$data['parts'] = $parts;
			$data['route'] = implode('/', $parts);
			
		}else{
						
			$data['is_seo'] = false;
			
			if ($_SERVER['REQUEST_URI'] == '/') {
				$data['is_seo'] = true;
			}
		}
		
		return $data;
		
	}
	
	private function get_end_data(){
		
		$data = array('code' => 'uk-ua', 'default' =>  true, 'prefix' => 'ua');
		
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		
		$all_langs = array(
			array('code' => 'en-gb', 'default' => false, 'prefix' => 'en'),
			array('code' => 'uk-ua', 'default' =>  true, 'prefix' => 'ua'),
		);
		
		foreach ($languages as $lang_db) {
			foreach ($all_langs as $lang) {
				if ($lang_db['code'] === $lang['code']) {
					$this->available_langs[] = $lang;
				}
			}
		}
		
		if (isset($_COOKIE['language'])) {
			foreach ($this->available_langs as $lang) {
				if ($_COOKIE['language'] == $lang['code']) {
					$data = $lang;
				}
			}
		}else{
			foreach ($this->available_langs as $lang) {
				if ($lang['default']) {
					$data = $lang;
				}
			}
		}
		
		return $data;
		
	}
	
	public function set_lang() {

		$out = array(
			'success' => false,
		);

		if (isset($this->request->post['lang'])) {
			$lang = $this->request->post['lang'];
			if ($lang == 'uk-ua' || $lang == 'en-gb') {
			
				setcookie('language', '', -1, '/');
				setcookie('language', $lang, time() + 60 * 60 * 24 * 30, '/');
				$this->session->data['language'] = $lang;
				$out['success'] = true;
			}
		}

		$this->response->setOutput(json_encode($out));
		return $out;
	}
}

<?php
class ControllerStartupMultiLanguageUrl extends Controller {

	private $defaultLanguage;

	public function __construct($registry) {
		parent::__construct($registry);
		// Registering the class with a key (multiLanguageUrl).
		// To access this class via $this->multiLanguageUrl->{methods} from other controllers
		$registry->set('multiLanguageUrl', $this);
		$this->defaultLanguage = $this->config->get('config_language');
	}

	public function index() {
		if (isset($this->request->get['_route_'])) {

			$languages = $this->getLanguages();

			// if multilingualism is not used. To stop execute the function
			if (count($languages) === 1) return;
			
			$parts = explode('/', $this->request->get['_route_']);

			foreach($languages as $lang) {
				if ($this->defaultLanguage !== $lang['code']) { // execute if url has not (default language) path in route
					$code = explode('-', $lang['code']);

					if ($code[0] === $parts[0]) {

						// set for links (lang parametr)
						$this->url->setParamLanguage($parts[0]);
						
						// remove $_get parameter _route_ if _route_ is equal index.php. For correct working links to which not applied seo url
						if ($parts[1] === 'index.php') {
							unset($this->request->get['_route_']);
							return; // exit from function
						}

						// replace (language code) to empty string in get parameter _route_ for correct working seo url and system opencart
						$this->request->get['_route_'] = str_ireplace($parts[0].'/', '' , $this->request->get['_route_']);
					}

					return;  // exit from function
				}
			}
		}
	}

	public function getDefaultLanguage() {
		return $this->defaultLanguage;
	}

	private function getLanguages() {
		$this->load->model('localisation/language');

		return $this->model_localisation_language->getLanguages();
	}

	private function getHostUrl() {
		return str_replace(["https://", "http://"], "", $this->config->get('site_url'));
	}

	private function getParamsFromUrl($url) {
		$siteUrl = $this->getHostUrl();
		$currentUrl = str_replace(["https://", "http://"], "",  $url);

		return substr($currentUrl, strlen($siteUrl));
	}

	public function getLanguageCodeFromUrl() {
		$host = rtrim($this->getHostUrl(), '/'); // remove last '/'
		$paths = explode('/', $host);
		array_shift($paths); // remove host

		$urlPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); // remove first '/' and last '/'
		$urlPath = explode('/', $urlPath);

		foreach($paths as $key => $path) {
			if ($path !== $urlPath[$key]) return;

			unset($urlPath[$key]);
		}

		$urlPath = array_values($urlPath); // reindex keys of array

		$langFromUrl = isset($urlPath[0]) ? $urlPath[0] : false;

		if ($langFromUrl) {
			$languages = $this->getLanguages();

			foreach($languages as $language) {
				$lang = explode('-', $language['code'])[0];
				$defaultLanguage = explode('-', $this->defaultLanguage)[0];

				if ($langFromUrl === $lang && $langFromUrl !== $defaultLanguage) {
					return $language['code'];
				}
			}
		}

		return $this->config->get('config_language'); // if it is not correct language, return default language
	}

	private function removeLanguageFromUrlParams($urlParams) {
		$languages = $this->getLanguages();
		$langCode = $this->getLanguageCodeFromUrl();
		
		foreach($languages as $language) {
			if ($langCode === $language['code'] && $langCode !== $this->defaultLanguage) {
				$langCode = explode('-', $langCode)[0];
				return substr($urlParams, strlen($langCode) + 1);
			}
		}

		return $urlParams; // if (language) is not in url or (language) in url is not correct. That return got argument urlParams
	}
	
	public function generateLinkWithActiveLangInUrl($url, $lang) {

		$host = $this->getHostUrl();

		if ($this->defaultLanguage !== $lang) {
			$currentLang = explode('-', $lang)[0];
			$params = $this->getParamsFromUrl($url);
			$params = $this->removeLanguageFromUrlParams($params);

			return 'http://' . $host . $currentLang . '/' . $params;
		}

		$params = $this->getParamsFromUrl($url);
		$params = $this->removeLanguageFromUrlParams($params);
		
		return 'http://' . $host . $params;
	}
}
<?php


/**
 * Page rendering.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Page.php 17831 2010-07-29 15:39:19Z akm $}
 */

class Aitsu_Core_Rendering_Page {

	protected $idartlang;
	protected $pageContent = null;
	protected $db;

	protected function __construct($idartlang) {

		$this->idartlang = $idartlang;
	}

	public static function factory($idartlang) {

		$instance = new self($idartlang);

		return $instance;
	}

	private function _render() {

		$cache = Aitsu_Cache :: getInstance('Aitsu_Core_Rendering_Page_' . $this->idartlang);
		if ($cache->isValid()) {
			$template = unserialize($cache->load());
		} else {
			$template = Aitsu_Core_Rendering_Template :: factory($this->idartlang);
			$template->setContext($this->_evaluateContext(), true);

			$cache->setLifetime(60 * 60 * 24 * 365);
			$cache->save(serialize($template), array (
				'type_basic'
			));
		}

		return $template->render();
	}

	public function getPage() {

		if ($this->pageContent == null) {
			$this->pageContent = $this->_render();
		}

		return $this->pageContent;
	}

	protected function _evaluateContext() {

		$reg = Aitsu_Registry :: get();

		foreach ($reg->cfg as $key => $value) {
			$returnValue[$key] = $value;
		}

		$returnValue['edit'] = false;

		$returnValue['client'] = $reg->config->sys->client;

		return $returnValue;
	}
}
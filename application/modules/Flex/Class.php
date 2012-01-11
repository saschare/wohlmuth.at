<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Flex_Class extends Aitsu_Module_Tree_Abstract {

	protected $_renderOnlyAllowed = true;
	protected $_allowEdit = false;

	protected function _init() {

		$view = $this->_getView();
		$view->id = $this->_index;
		$view->idartlang = Aitsu_Registry :: get()->env->idartlang;
	}

	protected function _main() {

		$this->_saveContent();

		$view = $this->_getView();
		$view->id = $this->_index;
		$view->idartlang = Aitsu_Registry :: get()->env->idartlang;
		$view->content = $this->_loadContent();
		$view->availableModules = Aitsu_Config :: get('flex')->toArray();

		$content = $this->_loadContent();

		if (Aitsu_Application_Status :: isEdit()) {
			$parts = preg_split('/(?:\\n\\r?){2,}/s', $content);
			$view->content = array ();
			for ($i = 0; $i < count($parts); $i++) {
				$part = trim($parts[$i]);
				if (!empty ($part)) {
					$view->content[] = (object) array (
						'textile' => implode("\n\n", array_slice($parts, $i)),
						'html' => Thresholdstate_Textile :: textile($part)
					);
				}
			}
			return $view->render('index.phtml');
		}

		return Thresholdstate_Textile :: textile($content);
	}

	protected function _cachingPeriod() {
		/*
		 * 1 year.
		 */
		return 60 * 60 * 24 * 365;
	}

	protected function _saveContent() {

		if (!Aitsu_Application_Status :: isEdit() || !isset ($_POST['edit']) || !$_POST['edit'] == 1) {
			return;
		}

		/*
		 * Editing is done here.
		 */

		trigger_error(var_export($_POST, true));
	}

	protected function _loadContent() {

		return Aitsu_Content :: get($this->_index, Aitsu_Content :: PLAINTEXT, null, null, 0);
	}

}
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

		if (Aitsu_Application_Status :: isEdit()) {
			// Aitsu_Util_Javascript :: addReference('/res/jQuery/1.6.2/jquery-ui-1.8.16.custom.min.js');
			// Aitsu_Util_Javascript :: add($view->render('js.phtml'));
		}
	}

	protected function _main() {

		$this->_saveContent();

		$view = $this->_getView();
		$view->id = $this->_index;
		$view->idartlang = Aitsu_Registry :: get()->env->idartlang;
		$view->content = $this->_loadContent();
		$view->availableModules = Aitsu_Config :: get('flex')->toArray();

		$in = $in =<<<EOF

A *simple* example.

an an new paragraph.
an just an new line.

notextile. <div>das ist ein test.</div>

EOF;

		// return Thresholdstate_Textile :: textile($in);

		return $view->render('index.phtml');
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

		/*
		 * Loading is done here.
		 */

		return null;
	}

}
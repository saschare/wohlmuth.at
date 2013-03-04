<?php


/**
 * @author Christian Kehres, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, webtischlerei
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Language_Selector_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Language.Selector', true);

		$view = $this->_getView();

		$languages = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	idlang, ' .
		'	name, ' .
		'	longname ' .
		'from _lang ' .
		'where ' .
		'	idclient = :idclient', array (
			':idclient' => Aitsu_Config :: get('sys.client')
		));

		foreach ($languages as $language) {
			$language = (object) $language;
			Aitsu_Core_Navigation_Language :: getInstance()->registerLang($language->idlang, $language->name, $language->longname);
		}

		$view->langs = Aitsu_Core_Navigation_Language :: getInstance()->getLangs();

		$output = $view->render('index.phtml');

		return $output;
	}

}
?>

<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Adm_Script_Synchronize_Database_Structure extends Aitsu_Adm_Script_Abstract {

	protected $_methodMap = array ();
	protected $_xml = null;

	public static function getName() {

		return Aitsu_Translate :: translate('Synchronize database structure');
	}

	protected function _init() {

		$this->_methodMap = array (
			'_removeConstraints',
			'_removeIndexes',
			'_removeViews',
			'_removeEmptyTables'
		);

		$this->_xml = DOMDocument :: loadXML('<database></database>');
		$xmls = Aitsu_Util_Dir :: scan(APPLICATION_PATH, 'database.xml');
		foreach ($xmls as $xml) {
			$dom = DOMDocument :: load($xml)->documentElement;
			foreach ($dom->childNodes as $node) {
				$node = $this->_xml->importNode($node, true);
				$this->_xml->documentElement->appendChild($node);
			}
		}

		$tables = $this->_xml->getElementsByTagName('table');

		for ($i = 0; $i < $tables->length; $i++) {
			$this->_methodMap[] = '_restoreTables';
		}

		$this->_methodMap[] = '_restoreIndexes';
		$this->_methodMap[] = '_restoreConstraints';
		$this->_methodMap[] = '_restoreViews';
	}

	protected function _hasNext() {

		if ($this->_currentStep < count($this->_methodMap)) {
			return true;
		}

		return false;
	}

	protected function _next() {

		return 'Next line to be executed.';
	}

	protected function _executeStep() {

		$method = $this->_methodMap[$this->_currentStep];
		$response = call_user_func_array(array (
			$this,
			$method
		), array ());

		return Aitsu_Adm_Script_Response :: factory($response);
	}

	protected function _removeConstraints() {

		return 'constraints removed';
	}

	protected function _removeIndexes() {

		return 'indexes removed';
	}

	protected function _removeViews() {

		return 'views removed';
	}

	protected function _removeEmptyTables() {

		return 'empty tables removed';
	}

	protected function _restoreTables() {

		$currentIndex = $this->_currentStep - 4;
		$table = $this->_xml->getElementsByTagName('table')->item($currentIndex);

		return $table->attributes->getNamedItem('name')->nodeValue . ' restored';
	}

	protected function _restoreIndexes() {

		return 'indexes restored';
	}

	protected function _restoreConstraints() {

		return 'constraints restored';
	}

	protected function _restoreViews() {

		trigger_error(var_export($this->_xml->saveXML(), true));

		return 'views restored';
	}

}
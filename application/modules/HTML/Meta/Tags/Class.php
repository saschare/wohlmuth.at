<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Meta_Tags_Class extends Aitsu_Module_Abstract {

	protected $_allowEdit = false;

	protected function _init() {

		$output = '';
		if ($this->_get('HTML.Meta.Tags', $output)) {
			return $output;
		}

		$meta = Aitsu_Db :: fetchRow('' .
		'select * from _art_meta ' .
		'where ' .
		'	idartlang = :idartlang', array (
			':idartlang' => Aitsu_Registry :: get()->env->idartlang
		));

		if (Aitsu_Config :: get('honeytrap.keyword') != null) {
			$honeyTraps = array_flip(Aitsu_Config :: get('honeytrap.keyword')->toArray());
			if (count(array_intersect_key($honeyTraps, $_GET)) > 0) {
				$meta['robots'] = (object) array (
					'value' => 'noindex'
				);
			}
		}

		$view = $this->_getView();
		$view->meta = $meta;

		$output = $view->render('index.phtml');

		$this->_save($output, 'eternal');

		return $output;
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}

}
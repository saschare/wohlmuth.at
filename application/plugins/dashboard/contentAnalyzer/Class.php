<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class ContentAnalyzerDashboardController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cd011a7-b334-43f8-bc6a-0a5f7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register() {

		return (object) array (
			'name' => 'contentAnalyzer',
			'tabname' => Aitsu_Translate :: _('Analyzer'),
			'enabled' => true,
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->numberOfCategories = Aitsu_Db :: fetchOne('' .
		'select count(*) from _cat_lang ' .
		'where idlang = :idlang', array (
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		$this->view->numberOfArticles = Aitsu_Db :: fetchOne('' .
		'select count(*) from _art_lang ' .
		'where ' .
		'	idlang = :idlang ' .
		'	and online = 1', array (
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		$this->view->categoriesWithoutIndex = Aitsu_Db :: fetchOne('' .
		'select count(*) from _cat_lang ' .
		'where ' .
		'	idlang = :idlang ' .
		'	and startidartlang is null', array (
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		$this->view->duplicateUrls = Aitsu_Db :: fetchOne('' .
		'select count(distinct url) from _cat_lang ' .
		'where ' .
		'	idlang = :idlang ' .
		'group by ' .
		'	url ' .
		'having count(idcatlang) > 1', array (
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));
		if (empty($this->view->duplicateUrls)) {
			$this->view->duplicateUrls = 0;
		}
	}
}
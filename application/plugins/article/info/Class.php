<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class InfoArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e53761e-df84-4824-b5a0-0def7f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		$pos = self :: getPosition($idart, 'info');
		$pos = empty ($pos) ? 0 : $pos;

		return (object) array (
			'name' => 'info',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Info'),
			'enabled' => true,
			'position' => $pos,
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');
		$idlang = Aitsu_Registry :: get()->session->currentLanguage;

		$this->view->props = Aitsu_Db :: fetchRow('' .
		'select distinct ' .
		'	artlang.idart \'Idart\', ' .
		'	artlang.idlang \'Idlang\', ' .
		'	artlang.idartlang \'Idartlang\', ' .
		'	catlang.idcat \'Idcat\', ' .
		'	catlang.idcatlang \'Idcatlang\', ' .
		'	concat(lower(artlang.urlname), \'.html\') \'URL (page only)\', ' .
		'	concat(\'/\', lower(catlang.url), \'/\', lower(artlang.urlname), \'.html\') \'URL (without language)\', ' .
		'	artlang.title \'Title\', ' .
		'	artlang.pagetitle \'Page title\', ' .
		'	artlang.teasertitle \'Teaser title\', ' .
		'	if(artlang.online > 0,\'yes\',\'no\') \'Online\', ' .
		'	if(catlang.public > 0,\'all\',\'Closed user group\') \'Access\', ' .
		'	artlang.created \'Created\', ' .
		'	artlang.lastmodified \'Last modified\' ' .
		'from _art_lang artlang ' .
		'left join _cat_art catart on artlang.idart = catart.idart ' .
		'left join _cat_lang catlang on catart.idcat = catlang.idcat and catlang.idlang = artlang.idlang ' .
		'where ' .
		'	artlang.idart = :idart ' .
		'	and artlang.idlang = :idlang', array (
			':idart' => $id,
			':idlang' => $idlang
		));
		
	}

}
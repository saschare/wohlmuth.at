<?php


/**
 * Originally developped by Christian Kehres. Completely
 * refactored for aitsu 0.9.3.1 by Andreas Kummer.
 * 
 * @author Christian Kehres, webtischlerei.de
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19142 2010-10-04 09:49:15Z akm $}
 */

class ArticleimageArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cc5b434-a718-4835-aef0-17737f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'articleimage',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Article image'),
			'enabled' => self :: getPosition($idart, 'articleimage'),
			'position' => self :: getPosition($idart, 'articleimage'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$idart = $this->getRequest()->getParam('idart');
		$idartlang = Aitsu_Persistence_Article :: factory($idart)->load()->idartlang;

		$this->view->idartlang = $idartlang;

		$prop = Aitsu_Persistence_ArticleProperty :: factory($idartlang)->load();
		$filename = isset ($prop->articleProperty['mainImage']) ? $prop->articleProperty['mainImage'] : null;

		$this->view->image = Aitsu_Db :: fetchOne('' .
		'select media.mediaid ' .
		'from _media as media ' .
		'left join _art_lang as artlang on artlang.idart = media.idart ' .
		'where ' .
		'	artlang.idartlang = :idartlang ' .
		'	and media.filename = :filename ' .
		'order by ' .
		'	media.mediaid desc ' .
		'limit 0, 1', array (
			':idartlang' => $idartlang,
			':filename' => isset ($filename->value) ? $filename->value : ''
		));

		$this->view->files = Aitsu_Core_File :: getImages($idartlang);
	}

	public function listAction() {
		
		$this->_helper->viewRenderer->setNoRender(true);

		echo $this->view->partial('imageSelectRadio.phtml', array (
			'files' => Aitsu_Core_File :: getImages($this->getRequest()->getParam('idartlang'))
		));
	}

	public function saveAction() {
		
		$this->_helper->viewRenderer->setNoRender(true);
		
		$prop = Aitsu_Persistence_ArticleProperty :: factory($this->getRequest()->getParam('idartlang'))->load();
		$prop->setValue('articleProperty', 'mainImage', $this->getRequest()->getParam('articleimage'));
		$prop->save();
	}
}
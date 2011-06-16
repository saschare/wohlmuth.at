<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class TodoPluginController extends Aitsu_Adm_Plugin_Controller {

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public function indexAction() {

		header("Content-type: text/javascript");
	}

	public function storeAction() {

		$data = Aitsu_Db :: fetchAll('' .
		'SELECT' .
		'	todo.title, ' .
		'	todo.description, ' .
		'	todo.duedate, ' .
		'	art.pagetitle, ' .
		'	art.title as articletitle, ' .
		'	art.idart, ' .
		'	now() as today, ' .
		'	catlang.url ' .
		'from _todo todo ' .
		'left join _art_lang art on art.idartlang = todo.idartlang ' .
		'left join _cat_art catart on art.idart = catart.idart ' .
		'left join _cat_lang catlang on catart.idcat = catlang.idcat and catlang.idlang = :idlang ' .
		'where ' .
		'	todo.status = 0 ' .
		'	and todo.userid = :userid ' .
		'order by ' .
		'	todo.duedate asc', array (
			':userid' => Aitsu_Adm_User :: getInstance()->userid,
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		$this->_helper->json((object) array (
			'data' => $data
		));
	}

}
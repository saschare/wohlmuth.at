<?php


/**
 * @author Christian Kehres, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * 
 * @copyright Copyright &copy; 2011, webtischlerei
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class toDoListDashboardController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4df9e2fc-fac4-4b01-9db5-09167f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register() {

		return (object) array (
			'name' => 'toDoList',
			'tabname' => Aitsu_Translate :: _('To-do-List'),
			'enabled' => true,
			'id' => self :: ID
		);
	}

	public function indexAction() {

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
		'	now() as today ' .
		'from _todo todo ' .
		'left join _art_lang art on art.idartlang = todo.idartlang ' .
		'where ' .
		'	(todo.duedate < now() or todo.status = 0) ' .
		'	and todo.userid = :userid ' .
		'order by ' .
		'	todo.duedate asc', array (
			':userid' => Aitsu_Adm_User :: getInstance()->userid
		));

		$this->_helper->json((object) array (
			'data' => $data
		));
	}

}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class FreehandPluginController extends Aitsu_Adm_Plugin_Controller {

	protected $_user = null;

	public function init() {
		
		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public function indexAction() {

	}

	public function listAction() {

		$this->_helper->layout->disableLayout();

		$table = $this->getRequest()->getParam('table');
		$this->view->table = $table;

		if ($this->user->isAllowed(array (
				'area' => 'data.table.' . preg_replace('/^[a-zA-Z]*_/', '_', $table),
				'action' => 'view'
			)) || $this->user->isAllowed(array (
				'area' => 'data.table.' . preg_replace('/^[a-zA-Z]*_/', '_', $table),
				'action' => 'crud'
			))) {
			$this->view->rows = Aitsu_Persistence_Generic :: factory(null, $table)->getFiltered(array ());
		} else {
			$this->view->rows = array ();
		}
	}

	protected function _getTables() {
		
		$user = Aitsu_Adm_User :: getInstance();

		$results = Aitsu_Db :: fetchCol('' .
		'select table_name from information_schema.tables ' .
		'where table_schema = :schema ' .
		'order by table_name', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname
		));

		$tables = array();
		if ($results) {
			foreach ($results as $table) {
				$table = preg_replace('/^[a-zA-Z]*_/', '_', $table);
				if ($user->isAllowed(array (
						'area' => 'data.table.' . $table,
						'action' => 'view'
					)) || $user->isAllowed(array (
						'area' => 'data.table.' . $table,
						'action' => 'crud'
					))) {
					$tables[] = Aitsu_Db :: getInstance()->prefix($table);
				}
			}
		}
		
		return $tables;
	} 
}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class StoreController extends Zend_Controller_Action {

	public function init() {

		// TODO: add user access rules.

		$this->_helper->layout->disableLayout();

		$this->_filter = Aitsu_Util_ExtJs :: encodeFilters($this->getRequest()->getParam('filter'));
	}

	/**
	 * User data.
	 * @since 2.1.0.0 - 22.12.2010
	 */
	public function usersAction() {

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_User :: getStore(100, 0, $this->_filter)
		));
	}

	/**
	 * Role data.
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function rolesAction() {

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Role :: getStore(100, 0, $this->_filter)
		));
	}

	/**
	 * Privilge data.
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function privilegesAction() {

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Privilege :: getStore(100, 0, $this->_filter)
		));
	}

	/**
	 * Resource data.
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function resourcesAction() {

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Resource :: getStore(100, 0, $this->_filter)
		));
	}

	/**
	 * Clients data.
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function clientsAction() {

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Clients :: getStore(100, 0, $this->_filter)
		));
	}

	/**
	 * Language data.
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function languagesAction() {

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Language :: getStore(100, 0, $this->_filter)
		));
	}

	/**
	 * Language and client data (for the dropdown to choose 
	 * lang/client combination to work with).
	 * @since 2.1.0.0 - 24.12.2010
	 */
	public function clientslangsAction() {

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	lang.idlang, ' .
		'	lang.name langname, ' .
		'	client.name clientname ' .
		'from _lang lang ' .
		'left join _clients client on lang.idclient = client.idclient ');

		$data = array ();
		foreach ($results as $result) {
			$data[] = (object) array (
				'idlang' => $result['idlang'],
				'identifier' => $result['clientname'] . ' / ' . $result['langname']
			);
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}

}
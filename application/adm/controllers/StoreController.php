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

}
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
	 * Available sync langs.
	 * @since 2.1.0.0 - 27.12.2010
	 */
	public function synclangAction() {

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	idlang, ' .
		'	name ' .
		'from _lang ' .
		'where ' .
		'	idclient = :idclient ' .
		'	and idlang != :idlang', array (
			':idclient' => Aitsu_Registry :: get()->session->currentClient,
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		$data = array ();
		foreach ($results as $result) {
			$data[] = (object) array (
				'idlang' => $result['idlang'],
				'name' => $result['name']
			);
		}

		$this->_helper->json((object) array (
			'data' => $data
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

	public function synccontentAction() {

		$idcat = $this->getRequest()->getParam('idcat');
		$idlang = $this->getRequest()->getParam('idlang');
		$currentLang = Aitsu_Registry :: get()->session->currentLanguage;

		$data = array ();

		$cats = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	catlang.idcatlang, ' .
		'	catlang.idcat, ' .
		'	catlang.name ' .
		'from _cat_lang catlang ' .
		'left join _cat cat on catlang.idcat = cat.idcat ' .
		'left join _cat_lang scatlang on catlang.idcat = scatlang.idcat and scatlang.idlang = :clang ' .
		'where ' .
		'	cat.parentid = :parentid ' .
		'	and catlang.idlang = :idlang ' .
		'	and scatlang.idcat is null ' .
		'order by ' .
		'	cat.lft asc ' .
		'	', array (
			':parentid' => $idcat,
			':idlang' => $idlang,
			':clang' => $currentLang
		));

		if ($cats) {
			foreach ($cats as $cat) {
				$data[] = (object) array (
					'id' => $cat['idcatlang'],
					'idv' => $cat['idcat'],
					'type' => 'cat',
					'title' => $cat['name'],
					'pagetitle' => ''
				);
			}
		}

		$arts = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.idartlang, ' .
		'	artlang.idart, ' .
		'	artlang.title, ' .
		'	artlang.pagetitle ' .
		'from _art_lang artlang ' .
		'left join _cat_art catart on artlang.idart = catart.idart ' .
		'left join _art_lang sartlang on artlang.idart = sartlang.idart and sartlang.idlang = :clang ' .
		'where ' .
		'	artlang.idlang = :idlang ' .
		'	and catart.idcat = :idcat ' .
		'	and sartlang.idart is null ' .
		'order by ' .
		'	artlang.title ', array (
			':idcat' => $idcat,
			':idlang' => $idlang,
			'clang' => $currentLang
		));

		if ($arts) {
			foreach ($arts as $art) {
				$data[] = (object) array (
					'id' => $art['idartlang'],
					'idv' => $art['idart'],
					'type' => 'art',
					'title' => $art['title'],
					'pagetitle' => $art['pagetitle']
				);
			}
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}
}
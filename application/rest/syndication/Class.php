<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class RestSyndicationController extends Aitsu_Adm_Plugin_Controller {

	public function init() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	}

	public function indexAction() {

		echo 'OK';
	}

	public function treeAction() {

		$return = array ();
		$user = Aitsu_Adm_User :: getInstance();

		$id = $this->getRequest()->getParam('id');
		$id = $id == null ? 0 : $id;

		$locale = $this->getRequest()->getParam('locale');
		$type = $this->getRequest()->getParam('type');

		if ($locale == null) {
			$this->_helper->json($return);
		}

		if ($type == 'unk') {
			/*
			 * Client side the type is unknown, the service delivers clients
			 * with languages matching the given locale and the user is allowed
			 * to use.
			 */
			$clients = Aitsu_Db :: fetchAll('' .
			'select client.* ' .
			'from _clients client ' .
			'left join _lang lang on lang.idclient = client.idclient ' .
			'where lang.locale = :locale ' .
			'order by client.name asc', array (
				':locale' => $locale
			));

			if ($clients) {
				foreach ($clients as $client) {
					if ($user->isAllowed(array (
							'client' => $client['idclient']
						))) {
						$return[] = array (
							'name' => $client['name'],
							'type' => 'client',
							'id' => $client['idclient']
						);
					}
				}
			}

			$this->_helper->json($return);
		}

		if ($type == 'client') {
			$categories = Aitsu_Db :: fetchAll('' .
			'select distinct ' .
			'	cat.idcat, ' .
			'	catlang.name ' .
			'from _cat cat ' .
			'left join _cat_lang catlang on cat.idcat = catlang.idcat ' .
			'left join _lang lang on catlang.idlang = lang.idlang ' .
			'where ' .
			'	cat.parentid = :idcat ' .
			'	and cat.idclient = :idclient ' .
			'	and lang.locale = :locale ' .
			'order by ' .
			'	cat.lft asc ', array (
				':idcat' => 0,
				':locale' => $locale,
				':idclient' => $id
			));

			if ($categories) {
				foreach ($categories as $cat) {
					$return[] = (object) array (
						'name' => $cat['name'],
						'id' => $cat['idcat'],
						'type' => 'category'
					);
				}
			}

			$this->_helper->json($return);
		}

		if ($type == 'category') {
			$categories = Aitsu_Db :: fetchAll('' .
			'select distinct ' .
			'	cat.idcat, ' .
			'	catlang.name ' .
			'from _cat cat ' .
			'left join _cat_lang catlang on cat.idcat = catlang.idcat ' .
			'left join _lang lang on catlang.idlang = lang.idlang ' .
			'where ' .
			'	cat.parentid = :idcat ' .
			'	and lang.locale = :locale ' .
			'order by ' .
			'	cat.lft asc ', array (
				':idcat' => $id,
				':locale' => $locale
			));

			if ($categories) {
				foreach ($categories as $cat) {
					$return[] = (object) array (
						'name' => $cat['name'],
						'id' => $cat['idcat'],
						'type' => 'category'
					);
				}
			}

			if ($user->isAllowed(array (
					'resource' => array (
						'type' => 'cat',
						'id' => $id
					)
				))) {
				$pages = Aitsu_Db :: fetchAll('' .
				'select ' .
				'	artlang.* ' .
				'from _art_lang artlang ' .
				'left join _cat_art catart on artlang.idart = catart.idart ' .
				'left join _cat_lang catlang on catlang.idcat = catart.idcat ' .
				'left join _lang lang on catlang.idlang = lang.idlang ' .
				'where ' .
				'	catart.idcat = :idcat ' .
				'	and lang.locale = :locale ' .
				'order by ' .
				'	artlang.title asc', array (
					':idcat' => $id,
					':locale' => $locale
				));

				if ($pages) {
					foreach ($pages as $page) {
						$return[] = (object) array (
							'name' => $page['title'],
							'id' => $page['idartlang'],
							'type' => 'page'
						);
					}
				}
			}

			$this->_helper->json($return);
		}
	}
}
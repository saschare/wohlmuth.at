<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Navigation.php 18379 2010-08-27 11:05:16Z akm $}
 */

class Aitsu_Adm_Controller_Plugin_Clientlang extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		$clients = Aitsu_Persistence_Clients :: getAll();
		if (empty ($clients)) {
			/*
			 * No clients available. Probably a new system.
			 */
			Aitsu_Persistence_Clients :: factory()->setValues(array (
				'name' => 'Client'
			))->save();
			$clients = Aitsu_Persistence_Clients :: getAll();
			Aitsu_Registry :: get()->session->currentClient = $clients[0]->idclient;
		}

		Zend_Registry :: set('clients', $clients);

		if (!isset (Aitsu_Registry :: get()->session->currentClient)) {
			Aitsu_Registry :: get()->session->currentClient = $clients[0]->idclient;
		}

		if ($request->getParam('setCurrentClient') != null) {
			Aitsu_Registry :: get()->session->currentClient = $request->getParam('setCurrentClient');
		}

		$langs = Aitsu_Persistence_Language :: getByClient(Aitsu_Registry :: get()->session->currentClient);
		if (empty ($langs)) {
			/*
			 * No languages available for the given client.
			 */
			try {
				Aitsu_Persistence_Language :: factory()->setValues(array (
					'idclient' => Aitsu_Registry :: get()->session->currentClient,
					'name' => 'Language ' . Aitsu_Registry :: get()->session->currentClient,
					'active' => 1
				))->save();
				$langs = Aitsu_Persistence_Language :: getByClient(Aitsu_Registry :: get()->session->currentClient);
				Aitsu_Registry :: get()->session->currentLanguage = $langs[0]->idlang;
			} catch (Exception $e) {
				echo $e->getMessage();
				exit;
			}
		}
		Zend_Registry :: set('langs', $langs);

		if (!isset (Aitsu_Registry :: get()->session->currentLanguage)) {
			Aitsu_Registry :: get()->session->currentLanguage = $langs[0]->idlang;
		}

		if ($request->getParam('setCurrentLanguage') != null) {
			Aitsu_Registry :: get()->session->currentLanguage = $request->getParam('setCurrentLanguage');
		}

		Aitsu_Registry :: get()->env->idlang = Aitsu_Registry :: get()->session->currentLanguage;
	}
}
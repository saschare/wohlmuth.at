<?php


/**
 * Execution context of a module.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Context.php 17657 2010-07-21 10:44:58Z akm $}
 */

class Aitsu_Core_Module_Context {

	protected $idartlang;
	protected $context;

	protected function __construct($idart, $idlang) {

		if ($idlang == null) {
			/*
			 * $idart is assumed to be $idartlang.
			 */
			$this->idartlang = $idart;
		} else {
			$this->idartlang = Aitsu_Db :: fetchOne('' .
			'select idartlang from _art_lang ' .
			'where ' .
			'	idart = ? ' .
			'	and idlang = ? ', array (
				$idart,
				$idlang
			));
		}

		$this->_restoreContext();
	}

	protected static function getInstance($idart, $idlang = null) {

		static $instance = array ();

		$token = $idart . '-' . ($idlang == null ? '0' : $idlang);

		if (!isset ($instance[$token])) {
			$instance[$token] = new self($idart, $idlang);
		}

		return $instance[$token];
	}

	public static function get($idart, $idlang = null) {

		return self :: getInstance($idart, $idlang)->context;
	}

	protected function _restoreContext() {

		$article = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	artlang.idart, ' .
		'	artlang.idlang, ' .
		'	artlang.idlang as lang, ' .
		'	artlang.idartlang, ' .
		'	client.idclient, ' .
		'	client.idclient as client, ' .
		'	client.config as config, ' .
		'	catart.idcat ' .
		'from _art_lang as artlang ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'left join _lang as lang on artlang.idlang = lang.idlang ' .
		'left join _clients as client on lang.idclient = client.idclient ' .
		'where idartlang = ? ', array (
			$this->idartlang
		));

		if ($article) {
			foreach ($article as $key => $value) {
				$this->context[$key] = $value;
			}
		}

		$this->context['edit'] = false;

		try {
			$config = Aitsu_Config_Ini :: getInstance('clients/' . $article['config']);	
			$this->context['config'] = $config;
		} catch (Exception $e) {
			$config = null;
		}
	}
}
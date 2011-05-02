<?php


/**
 * aitsu user.
 * @version $Id: User.php 18145 2010-08-16 15:47:12Z akm $
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Core_User {

	protected $login = 'guest';
	protected $userId = 0;
	protected $firstName = '';
	protected $lastName = '';
	protected $client;
	protected $allowedIdartlangs = array ();

	protected function __construct() {

		$this->client = Aitsu_Registry :: get()->config->sys->client;

		if (isset ($_GET['logout']) && $_GET['logout'] && isset (Aitsu_Registry :: get()->session->frontendUser->userId)) {
			Aitsu_Registry :: get()->session->frontendUser->userId = null;
		}

		if (isset (Aitsu_Registry :: get()->session->frontendUser->userId)) {
			$this->userId = Aitsu_Registry :: get()->session->frontendUser->userId;
		}
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function login($login, $password) {

		$this->login = $login;
		$this->password = $password;

		$this->_authenticate();
	}

	protected function _authenticate() {

		$user = Aitsu_Db :: fetchOne('' .
		'select idfrontenduser from _frontendusers ' .
		'where ' .
		'	username = ? ' .
		'	and password = ? ' .
		'	and active = 1 ' .
		'	and (' .
		'		now() between valid_from and valid_to ' .
		'		or (' .
		'			valid_from is null ' .
		'			and valid_to is null ' .
		'		) ' .
		'	) ' .
		'limit 0, 1 ', array (
			urlencode($this->login),
			md5($this->password)
		));

		$this->userId = $user != false ? $user : 0;

		Aitsu_Registry :: get()->session->frontendUser = (object) array (
			'userId' => $this->userId
		);
	}

	public function getUserId() {

		return $this->userId;
	}

	public static function hasAccess($idartlang, $isIdart = false) {

		if (Aitsu_Core_Backend_User :: getInstance()->getUserId() != null) {
			return true;
		}

		if ($isIdart) {
			$idartlang = Aitsu_Db :: fetchOne('' .
			'select idartlang from _art_lang ' .
			'where ' .
			'	idart = ? ' .
			'	and online = 1 ' .
			'limit 0, 1', array (
				$idartlang
			));
		}

		$instance = self :: getInstance();

		if (in_array($idartlang, $instance->allowedIdartlangs)) {
			return true;
		}

		if (Aitsu_Db :: fetchOne("" .
			"select count(*) " .
			"from _art_lang as artlang " .
			"left join _cat_art as catart on artlang.idart = catart.idart " .
			"left join _cat_lang as catlang on catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang " .
			"where " .
			"	catlang.public = 1 " .
			"	and artlang.idartlang = ? " .
			"", array (
				$idartlang
			)) > 0) {
			/*
			 * The requested idartlang is either public or not existing.
			 */
			$instance->allowedIdartlangs[] = $idartlang;
			return true;
		}

		/*if (Aitsu_Db :: fetchOne("" .
			"select count(*) " .
			"from _art_lang as artlang " .
			"left join _cat_art as catart on artlang.idart = catart.idart " .
			"left join _cat_lang as catlang on catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang " .
			"left join _frontendpermissions as perm on " .
			"	perm.idlang = artlang.idlang " .
			"	and perm.plugin = 'category' " .
			"	and ( " .
			"		perm.item = catlang.idcatlang " .
			"		or perm.item = ? " .
			"		) " .
			"left join _frontendgroupmembers as members on perm.idfrontendgroup = members.idfrontendgroup " .
			"where " .
			"	artlang.idartlang = ? " .
			"	and members.idfrontenduser = ? " .
			"", array (
				'__GLOBAL__',
				Aitsu_Registry :: get()->env->idartlang,
				$instance->userId
			)) > 0) {
			$instance->allowedIdartlangs[] = $idartlang;
			return true;
		}*/
		// TODO: Check for rights has to be implemented.

		return false;
	}
}
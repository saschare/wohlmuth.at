<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/*
CREATE TABLE IF NOT EXISTS `con_honeytrap` (
  `trapid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`trapid`),
  KEY `ip` (`ip`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
*/

class Aitsu_Persistence_Honeytrap extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
	}

	public static function factory($id = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance = new self($id);
		}

		return $instance;
	}

	public function load() {

		if ($this->_id == null || $this->_data !== null) {
			return $this;
		}

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select * from _honeytrap where trapid = :id', array (
			':id' => $this->_id
		));

		return $this;
	}

	public function __get($key) {

		if ($this->_data === null) {
			$this->load();
		}

		if (!isset ($this->_data[$key])) {
			return null;
		}

		return stripslashes($this->_data[$key]);
	}

	public function __set($key, $value) {

		if ($this->_data === null) {
			$this->_data = array ();
		}

		$this->_data[$key] = $value;
	}

	public function save() {

		if (empty ($this->_data)) {
			return;
		}

		Aitsu_Db :: put('_honeytrap', 'trapid', $this->_data);
	}

	public function getCountFor($ip) {

		return Aitsu_Db :: fetchOne('' .
		'select count(*) from _honeytrap ' .
		'where ' .
		'	ip = :ip ' .
		'	and date_add(created interval 3 month) > now() ', array (
			':ip' => $ip
		));
	}

	public function getWeight($ip) {

		if (Aitsu_Db :: fetchOne('select count(*) from _honeytrap where ip = :ip', array (
				':ip' => $ip
			)) == 0) {
			/*
			 * No entry. The weight is therefore 0.
			 */
			return 0;
		}

		return Aitsu_Db :: fetchOne('' .
		'select ' .
		'	sum(1 / (unix_timestamp(now()) - unix_timestamp(created)) * 100000) ' .
		'from _honeytrap ' .
		'where ' .
		'	ip = :ip ' .
		'	and created > date_sub(now(), interval 3 month) ', array (
			':ip' => $ip
		));
	}
}
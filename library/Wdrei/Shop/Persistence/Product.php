<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Wdrei_Shop_Persistence_Product extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = null;
	protected $_idlang = null;

	protected function __construct($id) {

		$this->_id = $id;
		$this->_idlang = Aitsu_Registry :: get()->session->currentLanguage;
		
		if (empty ($this->_idlang)) {
			$this->_idlang = Aitsu_Registry :: get()->env->idlang;
		}
	}

	public static function factory($id = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance[$id] = new self($id);
		}

		return $instance[$id];
	}

	public function load() {

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	product.*, ' .
		'	pname.name, ' .
		'	pname.description, ' .
		'	currency.code currencycode ' .
		'from _shop_product product ' .
		'left join _shop_product_name pname on product.productid = pname.productid and pname.idlang = :idlang ' .
		'left join _shop_tax_class taxclass on product.classid = taxclass.classid ' .
		'left join _shop_currency currency on product.currencyid = currency.currencyid ' .
		'where ' .
		'	product.idart = :idart ' .
		'', array (
			':idlang' => $this->_idlang,
			':idart' => $this->_id
		));

		if (!is_array($this->_data)) {
			$this->_data = array ();
		}

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
			return $this;
		}

		try {
			Aitsu_Db :: startTransaction();

			$this->_data['productid'] = Aitsu_Db :: put('_shop_product', 'productid', $this->_data);
			
			Aitsu_Db :: query('' .
			'insert into _shop_product_name ' .
			'(productid, idlang, name, description) ' .
			'values ' .
			'(:productid, :idlang, :name, :description) ' .
			'on duplicate key ' .
			'update ' .
			'	name = :name, ' .
			'	description = :description', array (
				':productid' => $this->_data['productid'],
				':idlang' => $this->_idlang,
				':name' => $this->_data['name'],
				':description' => $this->_data['description']
			));

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}

		return $this;
	}

	public function remove() {

		return $this;
	}

}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Wdrei_Shop_Persistence_Order extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
	}

	public static function factory($id = null) {

		static $instance = array ();

		if (isset (Aitsu_Registry :: get()->session->orderid)) {
			$id = Aitsu_Registry :: get()->session->orderid;
		} else {
			$id = Aitsu_Db :: query('' .
			'insert into _shop_order ' .
			'(created) values (now())')->getLastInsertId();
			Aitsu_Registry :: get()->session->orderid = $id;
		}

		if (!isset ($instance[$id])) {
			$instance[$id] = new self($id);
		}

		return $instance[$id];
	}

	public function load() {

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	* ' .
		'from _shop_order ' .
		'where ' .
		'	orderid = :orderid ' .
		'', array (
			':orderid' => $this->_id
		));
		
		$this->_data['items'] = array ();
		$items = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	product.*, ' .
		'	item.*, ' .
		'	name.* ' .
		'from _shop_item item ' .
		'left join _shop_product product on item.productid = product.productid ' .
		'left join _shop_product_name name on product.productid = name.productid and name.idlang = :idlang ' .
		'where ' .
		'	orderid = :orderid', array (
			':orderid' => $this->_id,
			':idlang' => Aitsu_Registry :: get()->env->idlang
		));
		if ($items) {
			foreach ($items as $item) {
				$this->_data['items'][] = (object) $item;
			}
		}

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
		
		if (is_array($this->_data[$key]) || is_object($this->_data[$key])) {
			return $this->_data[$key];
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

		return $this;
	}

	public function remove() {

		return $this;
	}

	public function addItem($productid, $amount, $price, $additionalInfo) {

		$product = Aitsu_Db :: fetchRow('' .
		'select * from _shop_product where productid = :productid', array (
			':productid' => (int) $productid
		));

		if (!$product) {
			return;
		}
		
		if (empty($price)) {
			$price = $product['price'];
		}

		if ($product['additionalinfo'] != 'never' || $product['variableprice']) {
			/*
			 * Product has to be placed as an own entity to allow to change its
			 * price or its additional info. We therefore have to make an insert
			 * into the database.
			 */
			Aitsu_Db :: put('_shop_item', 'itemid', array (
				'orderid' => $this->_id,
				'productid' => (int) $productid,
				'amount' => (float) $amount,
				'additionalinfo' => $additionalInfo,
				'price' => (float) $price
			));
		} else {
			/*
			 * If there is already an item with the same productid the two items
			 * have to be merged to one.
			 */
			if (Aitsu_Db :: fetchOne('' .
				'select count(*) from _shop_item ' .
				'where ' .
				'	orderid = :orderid ' .
				'	and productid = :productid', array (
					':orderid' => $this->_id,
					':productid' => $productid
				)) > 0) {
				/*
				 * There is already an item with the same productid. An update
				 * has to be made.
				 */
				Aitsu_Db :: query('' .
				'update _shop_item set ' .
				'	amount = amount + :amount ' .
				'where ' .
				'	orderid = :orderid ' .
				'	and productid = :productid', array (
					':orderid' => $this->_id,
					':productid' => $productid,
					':amount' => $amount
				));
			} else {
				/*
				 * There is no item of the specifed productid. Therefore an
				 * insert has to be made.
				 */
				Aitsu_Db :: put('_shop_item', 'itemid', array (
					'orderid' => $this->_id,
					'productid' => (int) $productid,
					'amount' => (float) $amount,
					'additionalinfo' => $additionalInfo,
					'price' => (float) $price
				));
			}
		}
	}

	public function removeItem($itemid) {

		Aitsu_Db :: query('' .
		'delete from _shop_item where itemid = :itemid', array (
			':itemid' => $itemid
		));
	}

	public function updateItem($itemid, $amount, $price, $additionalInfo) {

		Aitsu_Db :: query('' .
		'update _shop_item set ' .
		'	amount = :amount, ' .
		'	price = :price, ' .
		'	additionalinfo = :additionalinfo ' .
		'where ' .
		'	itemid = :itemid', array (
			':amount' => $amount,
			':price' => $price,
			':additionalinfo' => $additionalInfo,
			':itemid' => $itemid
		));
	}
}
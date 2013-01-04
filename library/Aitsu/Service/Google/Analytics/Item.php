<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Service_Google_Analytics_Item {

	protected $_data = array ();

	public static function add($orderId, $sku, $name, $category, $price, $quantity) {

		$instance = new self();

		$instance->_data['orderId'] = $orderId;
		$instance->_data['sku'] = $sku;
		$instance->_data['name'] = $name;
		$instance->_data['category'] = $category;
		$instance->_data['price'] = preg_replace('/[^0-9\\.]/', '', $price);
		$instance->_data['quantity'] = preg_replace('/[^0-9\\.]/', '', $quantity);

		return $instance;
	}

	public function getPush() {

		if (empty ($this->_data)) {
			return '';
		}

		array_walk($this->_data, array (
			$this,
			'_escapeForJs'
		));

		$return = "\t" . '_gaq.push([\'_addItem\',\'';
		$return .= implode('\',\'', $this->_data);
		$return .= '\']);' . "\n";

		return $return;
	}

	protected function _escapeForJs(& $val) {

		$val = preg_replace("/\r?\n/", "\\n", addslashes($val));
	}
}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Service_Google_Analytics_Transaction {

	protected $_transactionId = -1;
	protected $_data = array ();
	protected $_items = array ();

	private function __construct() {
	}

	protected static function getInstance($doNotCount = false) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		if (!$doNotCount) {
			$instance->_transactionId += 1;
		}

		return $instance;
	}

	public static function add($orderId, $affiliationDomain, $total, $tax, $shipping, $city, $state, $country) {

		$instance = Aitsu_Service_Google_Analytics_Transaction :: getInstance();

		/*
		 * Set a unique ID as order id if no value is specified.
		 */
		$orderId = empty ($orderId) ? uniqid() : $orderId;

		/*
		 * Take the affiliationDomain if not specified and available in the configuration.
		 * Otherwise use the current domain information.
		 */
		if (empty ($affiliationDomain)) {
			$affiliationDomain = Aitsu_Config :: get('google.analytics.affiliatedomain');
		}
		if (empty ($affiliationDomain) && isset ($_SERVER['HTTP_HOST'])) {
			$affiliationDomain = $_SERVER['HTTP_HOST'];
		}
		if (empty ($affiliationDomain) && isset ($_SERVER['SERVER_NAME'])) {
			$affiliationDomain = $_SERVER['SERVER_NAME'];
		}
		/*
		 * Set to domain.tld if still empty.
		 */
		$affiliationDomain = empty ($affiliationDomain) ? 'domain.tld' : $affiliationDomain;

		$instance->_data[$instance->_transactionId]['orderId'] = $orderId;
		$instance->_data[$instance->_transactionId]['affiliationDomain'] = $affiliationDomain;
		$instance->_data[$instance->_transactionId]['total'] = preg_replace('/[^0-9\\.]/', '', $total);
		$instance->_data[$instance->_transactionId]['tax'] = preg_replace('/[^0-9\\.]/', '', $tax);
		$instance->_data[$instance->_transactionId]['shipping'] = preg_replace('/[^0-9\\.]/', '', $shipping);
		$instance->_data[$instance->_transactionId]['city'] = $city;
		$instance->_data[$instance->_transactionId]['state'] = $state;
		$instance->_data[$instance->_transactionId]['country'] = $country;

		return $instance;
	}

	public function addItem($sku, $name, $category, $price, $quantity) {

		$this->_items[$this->_transactionId][] = Aitsu_Service_Google_Analytics_Item :: add($this->_data[$this->_transactionId]['orderId'], $sku, $name, $category, $price, $quantity);
	}

	public static function getPush() {

		$instance = Aitsu_Service_Google_Analytics_Transaction :: getInstance(true);

		if (empty ($instance->_data)) {
			return '';
		}

		$return = '';

		array_walk_recursive($instance->_data, array (
			$instance,
			'_escapeForJs'
		));

		foreach ($instance->_data as $key => $val) {
			$return .= "\t" . '_gaq.push([\'_addTrans\',\'';
			$return .= implode('\',\'', $instance->_data[$key]);
			$return .= '\']);' . "\n";

			if (isset ($instance->_items[$key])) {
				foreach ($instance->_items[$key] as $item) {
					$return .= $item->getPush();
				}
			}
		}
		
		$return .= "\t_gaq.push(['_trackTrans']);\n";

		return $return;
	}

	protected function _escapeForJs(& $val) {

		$val = preg_replace("/\r?\n/", "\\n", addslashes($val));
	}

}
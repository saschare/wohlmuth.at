<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Service_Google_Analytics_Transaction {

	protected $_data;
	
	public static function add($orderId, $affiliationDomain, $total, $tax, $shipping, $city, $state, $country) {
		
		$instance = new self();
		
		$instance->_data['orderId'] = $orderId;
		$instance->_data['affiliationDomain'] = $affiliationDomain;
		$instance->_data['total'] = $total;
		$instance->_data['tax'] = $tax;
		$instance->_data['shipping'] = $shipping;
		$instance->_data['city'] = $city;
		$instance->_data['country'] = $country;
		
		return $instance;
	}
	
	public function getPush() {
		
		if (empty($this->_data)) {
			return '';
		}
		
		$return = '_gaq.push([\'_addTrans\',';
		
		
		
		return $return;
	}
}
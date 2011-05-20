<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_Address_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Address',
			'description' => Aitsu_Translate :: translate('Inserts a microformat address'),
			'type' => 'Microformat',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db9401b-9370-4da0-96b7-0bf150431368'
		);
	}
		
	public static function init($context) {

		$index = $context['index'];

		$instance = new self();
		$view = $instance->_getView();
		
		$view->index = $index;

        $view->firstname = Aitsu_Content_Config_Text :: set($index, 'AddressFirstname', 'Firstname', 'Additional attributes');
        $view->lastname = Aitsu_Content_Config_Text :: set($index, 'AddressLastname', 'Lastname', 'Additional attributes');
        $view->company = Aitsu_Content_Config_Text :: set($index, 'AddressCompany', 'Company', 'Additional attributes');
        $view->street = Aitsu_Content_Config_Text :: set($index, 'AdressStreet', 'Street', 'Additional attributes');
        $view->zip = Aitsu_Content_Config_Text :: set($index, 'AddressZip', 'ZIP', 'Additional attributes');
        $view->city = Aitsu_Content_Config_Text :: set($index, 'AddressCity', 'City', 'Additional attributes');
        $view->state = Aitsu_Content_Config_Text :: set($index, 'AddressState', 'State', 'Additional attributes');
        $view->country = Aitsu_Content_Config_Text :: set($index, 'AddressCountry', 'Country', 'Additional attributes');
        $view->email = Aitsu_Content_Config_Text :: set($index, 'AddressEmail', 'EMail', 'Additional attributes');
        $view->webaddress = Aitsu_Content_Config_Text :: set($index, 'AddressWebaddress', 'Internet', 'Additional attributes');
        $view->telephone = Aitsu_Content_Config_Text :: set($index, 'AddressTelephone', 'Telephone', 'Additional attributes');

		if (!$view->webaddress) {
			if (Aitsu_Application_Status :: isEdit()) {
				return ':: Address ' . $index . ' ::';
			} else {
				return '';
			}
		}
        
		$output = $view->render('index.phtml');

		return $output;
	}
}
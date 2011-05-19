<?php


/**
 * @author Frank Ammari, meine experten GbR
 * @copyright Copyright &copy; 2010, meine experten GbR
 */

class Skin_Module_Address_Class extends Aitsu_Ee_Module_Abstract {

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
			if (Aitsu_Registry :: get()->env->edit == '1') {
				return '// Address ' . $index . ' //';
			} else {
				return '';
			}
		}
        
		$output = $view->render('index.phtml');

		return $output;
	}
}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Test_Google_Geolocation_Class extends Aitsu_Module_Abstract {

	protected function _main() {

		return '<pre>' . var_export(Aitsu_Service_Google_Geocode :: getInstance()->locate('brunnmattstrasse 13, thörishaus, schweiz'), true) . '</pre>';
	}

}
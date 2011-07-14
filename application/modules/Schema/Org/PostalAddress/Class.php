<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

include_once (APPLICATION_PATH . '/modules/Schema/Org/ContactPoint/Class.php');

class Module_Schema_Org_PostalAddress_Class extends Module_Schema_Org_ContactPoint_Class {

	protected function _init() {
	}

	protected function _main() {

		$view = $this->_getView();

		return $view->render('index.phtml');
	}

	protected function _getView() {

		$view = parent :: _getView();

		$countries = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	en, ' .
		'	iso2 ' .
		'from _geo_country ' .
		'order by ' .
		'	en asc');

		$countryData = array ();
		foreach ($countries as $country) {
			$countryData[str_replace("'", "\\'", $country['en'])] = $country['iso2'];
		}

		$view->addressCountry = Aitsu_Content_Config_Select :: set($this->_index, 'schema.org.PostalAddress.AddressCountry', 'Country', $countryData, 'PostalAddress');
		$view->addressLocality = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.PostalAddress.AddressLocality', 'Locality', 'PostalAddress');
		$view->addressRegion = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.PostalAddress.AddressRegion', 'Region', 'PostalAddress');
		$view->postOfficeBoxNumber = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.PostalAddress.PostOfficeBoxNumber', 'P.O. Box', 'PostalAddress');
		$view->postalCode = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.PostalAddress.PostalCode', 'Postal Code', 'PostalAddress');
		$view->streetAddress = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.PostalAddress.StreetAddress', 'Street', 'PostalAddress');

		return $view;
	}
}
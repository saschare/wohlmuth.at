<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Schema_Org_Item_Class extends Aitsu_Module_SchemaOrg_Abstract {

	protected function _init() {
	}

	protected function _main() {

		$availableTypes = array (
			'AggregateRating',
			'ContactPoint',
			'GeoCoordinates',
			'Organization',
			'PostalAddress',
			'Rating',
			'Thing'
		);
		sort($availableTypes);

		$types = array ();

		foreach ($availableTypes as $type) {
			$types[$type] = $type;
		}

		$schemaType = Aitsu_Content_Config_Select :: set($this->_index, 'schema.org.Item', 'Item type', $types, 'Schema.org Types');

		if (empty ($schemaType)) {
			$schemaType = 'Thing';
		}
		
		if (Aitsu_Application_Status :: isEdit()) {
			return '<div style="padding-top:5px;">_[Schema.Org.' . $schemaType . ':' . $this->_index . ']</div>';
		}

		return '_[Schema.Org.' . $schemaType . ':' . $this->_index . ']';
	}
}
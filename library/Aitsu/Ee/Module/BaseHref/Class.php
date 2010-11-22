<?php


/**
 * BaseHref ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17211 2010-06-18 15:17:12Z akm $}
 */

class Aitsu_Ee_Module_BaseHref_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'BaseHref',
			'description' => Aitsu_Translate :: _('Returns the specified path as a base href tag.'),
			'type' => 'Header',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725360-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('BaseHref', true);

		return '<base href="' . Aitsu_Registry :: get()->config->sys->webpath . '" />';
	}
}
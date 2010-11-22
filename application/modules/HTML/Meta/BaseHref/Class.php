<?php


/**
 * BaseHref ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19941 2010-11-18 19:13:51Z akm $}
 */

class Module_HTML_Meta_BaseHref_Class extends Aitsu_Ee_Module_Abstract {

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
			'id' => '4ce55bd6-c89c-435a-a004-09f97f000101'
		);
	}

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('HTML.Meta.BaseHref', true);

		return '<base href="' . Aitsu_Registry :: get()->config->sys->webpath . '" />';
	}
}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19941 2010-11-18 19:13:51Z akm $}
 */

class Module_HTML_Meta_CanonicalTag_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Canonical tag',
			'description' => Aitsu_Translate :: _('Returns the canonical tag of the current page.'),
			'type' => 'Navigation',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ce57475-49c8-4546-88a5-4bd97f000101'
		);
	}

	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('HTML.Meta.CanonicalTag', true);
		
		$instance = new self();
		
		$output = '';
		if ($instance->_get('CanonicalTag', $output)) {
			return $output;
		}
		
		$art = Aitsu_Persistence_Article :: factory(Aitsu_Registry :: get()->env->idart, Aitsu_Registry :: get()->env->idlang)->load();
		
		if ($art->startidartlang == $art->idartlang) {
			$output = '<link rel="canonical" href="{ref:idcat-' . $art->idcat . '}" />';
		} else {
			$output = '<link rel="canonical" href="{ref:idart-' . $art->idart . '}" />';
		}
		
		$instance->_save($output, 'eternal');
		
		return $output;
	}

}
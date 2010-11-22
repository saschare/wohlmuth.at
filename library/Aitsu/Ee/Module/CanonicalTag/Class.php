<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19657 2010-11-03 10:48:51Z akm $}
 */

class Aitsu_Ee_Module_CanonicalTag_Class extends Aitsu_Ee_Module_Abstract {

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
			'id' => '4cd138b3-ed74-401c-a4b6-0a587f000101'
		);
	}

	public static function init($context) {
		
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
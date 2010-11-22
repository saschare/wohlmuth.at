<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19947 2010-11-18 19:33:37Z akm $}
 */

class Module_HTML_Meta_Tags_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function about() {

		return (object) array (
			'name' => 'Meta tags',
			'description' => Aitsu_Translate :: translate('Returns the meta tags of the current article.'),
			'type' => 'Header',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ce57ea4-3890-4c80-9fd6-4c097f000101'
		);
	}

	public static function init($context) {

		$instance = new self();
		Aitsu_Content_Edit :: noEdit('HTML.Meta.Tags', true);

		$output = '';
		if ($instance->_get('MetaTags', $output)) {
			return $output;
		}

		$meta = Aitsu_Core_Article_Property :: factory()->getNamespace('MetaInfo');

		if (isset (Aitsu_Registry :: get()->config->honeytrap->keyword)) {
			$honeyTraps = array_flip(Aitsu_Registry :: get()->config->honeytrap->keyword->toArray());
			if (count(array_intersect_key($honeyTraps, $_GET)) > 0) {
				$meta['robots'] = (object) array (
					'value' => 'noindex'
				);
			}
		}

		$view = $instance->_getView();
		$view->meta = $meta;

		$output = $view->render('index.phtml');

		$instance->_save($output, 'eternal');

		return $output;
	}
}
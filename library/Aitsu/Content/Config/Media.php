<?php


/**
 * @author Christian Kehres, webtischlerei.de
 * @copyright Copyright &copy; 2010, webtischlerei.de
 */

class Aitsu_Content_Config_Media extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {

		return 'media.phtml';
	}

	public static function set($index, $name, $label) {

		$idart = Aitsu_Registry :: get()->env->idart;
		$instance = new self($index, $name);

		$instance->facts['tab'] = true; 
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'serialized';
		
		$instance->params['media'] = Aitsu_Persistence_View_Media :: ofCurrentArticle();

		return $instance->currentValue();
	}
}
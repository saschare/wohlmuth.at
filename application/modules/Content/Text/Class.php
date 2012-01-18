<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Content_Text_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$text = htmlentities(Aitsu_Content_Text :: get($this->_index, 0), ENT_COMPAT, 'UTF-8');

		$text = (empty ($text) && Aitsu_Registry :: isEdit()) ? Aitsu_LoremIpsum :: get(5) : $text;

		if (isset ($this->_params->tag)) {
			$output = '<' . $this->_params->tag . '>' . $text . '</' . $this->_params->tag . '>';
		} else {
			$output = $text;
		}

		return $output;
	}

}
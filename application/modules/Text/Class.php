<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Text_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$output = '';
		if ($instance->_get('Text', $output)) {
			return $output;
		}

		$text = htmlentities(Aitsu_Content_Text :: get($this->_index, 0), ENT_COMPAT, 'UTF-8');

		$text = (empty ($text) && Aitsu_Registry :: isEdit()) ? Aitsu_LoremIpsum :: get(5) : $text;

		if (isset ($params->tag)) {
			$output = '<' . $params->tag . '>' . $text . '</' . $params->tag . '>';
		} else {
			$output = $text;
		}

		if (Aitsu_Registry :: get()->env->edit == '1') {
			$output = '<code class="aitsu_params" style="display:none;">' . $this->_context['params'] . '</code>' . $output;
		}

		$this->_save($output, 'eternal');

		return $output;
	}

}
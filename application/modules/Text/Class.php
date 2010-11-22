<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17010 2010-06-10 16:11:15Z akm $}
 */

class Module_Text_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Text',
			'description' => Aitsu_Translate :: translate('Editable area rendered as a plain text area.'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ce57ea4-76ac-410b-91a6-4c097f000101'
		);
	}

	public static function init($context) {

		$instance = new self();
		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$output = '';
		if ($instance->_get('Text_' . $context['index'], $output)) {
			return $output;
		}

		$text = htmlentities(Aitsu_Content_Text :: get($index, 0), ENT_COMPAT, 'UTF-8');

		$text = (empty ($text) && Aitsu_Registry :: isEdit()) ? Aitsu_LoremIpsum :: get(5) : $text;

		if (isset($params->tag)) {
			$output = '<' . $params->tag . '>' . $text . '</' . $params->tag . '>';
		} else {
			$output = $text;
		}

		if (Aitsu_Registry :: get()->env->edit == '1') {
			$output = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $output;
		}

		$instance->_save($output, 'eternal');

		return $output;
	}

}
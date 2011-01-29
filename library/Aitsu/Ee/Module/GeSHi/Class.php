<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_GeSHi_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();
		Aitsu_Content_Edit :: noEdit('GeSHi', true);

		$lang = $context['index'];
		$code = $context['params'];

		$id = md5($lang . $code);

		$output = '';
		if ($instance->_get('Geshi_' . $id, $output)) {
			return $output;
		}

		$output = Aitsu_GeSHi :: parse($code, $lang);

		$instance->_save($output, 'eternal');

		return $output;
	}
}
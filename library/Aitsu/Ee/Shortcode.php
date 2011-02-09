<?php


/**
 * aitsu Shortcode evaluation.
 *
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 *
 * {@id $Id: Shortcode.php 17827 2010-07-29 13:11:30Z akm $}
 */

class Aitsu_Ee_Shortcode {

	protected function __construct() {
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * @deprecated 0.9.2 - 07.10.2010
	 */
	public function getContent($params) {

		return '';
	}

	public function evalModule($method, $params, $client, $index, $current = true) {

		$profileDetails = new stdClass();
		Aitsu_Profiler :: profile($method . ':' . $index);

		$returnValue = '';
		Aitsu_Content_Edit :: isBlock(true);

		$classFile0 = APPLICATION_PATH . "/skins/" . Aitsu_Registry :: get()->config->skin . "/module/{$method}/Class.php";
		$classFile1 = realpath(APPLICATION_PATH . '/../library/Local/Module/' . $method . '/Class.php');
		$classFile2 = realpath(APPLICATION_PATH . '/modules/' . str_replace('.', '/', $method) . '/Class.php');
		$classFile3 = realpath(APPLICATION_PATH . '/../library/Aitsu/Ee/Module/' . $method . '/Class.php');

		if (file_exists($classFile0)) {
			$profileDetails->source = 'Skin_Module_' . $method . '_Class';
			include_once $classFile0;
			$returnValue = call_user_func(array (
				$profileDetails->source,
				'init'
			), array (
				'index' => $index,
				'params' => $params
			));
		}
		elseif (file_exists($classFile1)) {
			$profileDetails->source = 'Local_Module_' . $method . '_Class';
			include_once ($classFile1);
			$returnValue = call_user_func(array (
				$profileDetails->source,
				'init'
			), array (
				'index' => $index,
				'params' => $params
			));
		}
		elseif (file_exists($classFile2)) {
			$profileDetails->source = 'Module_' . str_replace('.', '_', $method) . '_Class';
			include_once ($classFile2);
			$returnValue = call_user_func(array (
				$profileDetails->source,
				'init'
			), array (
				'index' => $index,
				'params' => $params
			));
		}
		elseif (file_exists($classFile3)) {
			$profileDetails->source = 'Aitsu_Ee_Module_' . $method . '_Class';
			include_once ($classFile3);
			$returnValue = call_user_func(array (
				$profileDetails->source,
				'init'
			), array (
				'index' => $index,
				'params' => $params
			));
		} else {
			Aitsu_Profiler :: profile($method . ':' . $index, (object) array (
				'source' => 'not found'
			));
			if (Aitsu_Registry :: isEdit()) {
				return '<strong>' . sprintf(Aitsu_Registry :: translator()->translate('// The ShortCode \'%s\' does not exist. //'), $method) . '</strong>';
			} else {
				return '';
			}
		}

		if (is_object($returnValue)) {
			$index = $returnValue->index;
			$returnValue = $returnValue->out;
		}

		Aitsu_Profiler :: profile($method . ':' . $index, $profileDetails);

		if (Aitsu_Registry :: isBoxModel() && !Aitsu_Content_Edit :: noEdit($method)) {
			$returnValue = '<shortcode method="' . $method . '" index="' . $index . '">' . $returnValue . '</shortcode>';
		} else
			if (Aitsu_Application_Status :: isStructured()) {
				$startmarker = '<!--fragement:start ' . $method . '-' . $index . '-' . Aitsu_Registry :: get()->env->idartlang . '"-->';
				$endmarker = '<!--fragement:end ' . $method . '-' . $index . '-' . Aitsu_Registry :: get()->env->idartlang . '"-->';
				$returnValue = $startmarker . $returnValue . $endmarker;
			} else
				if (Aitsu_Registry :: isEdit() && !Aitsu_Content_Edit :: noEdit($method)) {
					$isBlock = Aitsu_Content_Edit :: isBlock();
					if ($isBlock === true) {
						$returnValue = '<div id="' . $method . '-' . $index . '-' . Aitsu_Registry :: get()->env->idartlang . '" class="aitsu_editable"><div class="aitsu_hover">' . $returnValue . '</div></div>';
					} else {
						$returnValue = '<span id="' . $method . '-' . $index . '-' . Aitsu_Registry :: get()->env->idartlang . '" class="aitsu_editable" style="display:inline;"><span class="aitsu_hover">' . $returnValue . '</span></span>';
					}
				}

		return $returnValue;
	}

	/**
	 * The magic __get method is some kind of misused in this case. Instead
	 * of allowing access to non-public members of the object it returns the
	 * names of the parameters of the specified method. False is returned, if
	 * the method does not exist and null, if the specification is not made
	 * or the method does not expect any parameters. In any other case the
	 * method returns an array containing the names of the parameters that are
	 * expected by the method.
	 * @param String Name of the method.
	 * @return Mixed False, null or an array.
	 *
	 * @deprecated 0.9.3 - 13.10.2010
	 */
	public function __get($key) {

		return false;
	}
}
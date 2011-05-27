<?php


/**
 * aitsu Diagnose controller.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Controller.php 17023 2010-06-10 18:14:54Z akm $}
 */

class Aitsu_Core_Diagnose_Controller {

	public static function getAvailableDiagnoses() {

		$return = array ();

		$diagnoses = scandir(APPLICATION_PATH . '/diagnoses');

		foreach ($diagnoses as $diagnose) {
			$pathInfo = pathinfo($diagnose);
			if (isset($pathInfo['extension']) && $pathInfo['extension'] == 'php') {
				include (APPLICATION_PATH . '/diagnoses/' . $diagnose);
				$className = self :: _getClassName($diagnose);
				if (in_array('Aitsu_Core_Diagnose_Diagnose_Interface', class_implements($className))) {
					$details = call_user_func(array (
						self :: _getClassName($diagnose),
						'register'
					));
					$return[$details->package][$details->name] = array(self :: _getClassName($diagnose), isset($details->confirmation) ? $details->confirmation : null);
				}
			}
		}

		foreach ($return as $key => &$value) {
			ksort($value);
		}
		ksort($return);

		return $return;
	}

	protected static function _getClassName($filename) {
		return basename($filename, '.php');
	}

	public static function getDiagnoseDetails($diagnose) {

		if (!is_file(APPLICATION_PATH . '/diagnoses/' . $diagnose . '.php')) {
			return null;
		}

		if (!class_exists($diagnose)) {
			include (APPLICATION_PATH . '/diagnoses/' . $diagnose . '.php');
		}

		if (in_array('Aitsu_Core_Diagnose_Diagnose_Interface', class_implements($diagnose))) {
			return call_user_func(array (
				self :: _getClassName($diagnose),
				'register'
			));
		}

		return null;
	}

	public static function getResult($diagnose, $step) {

		if (!is_file(APPLICATION_PATH . '/diagnoses/' . $diagnose . '.php')) {
			return null;
		}

		include (APPLICATION_PATH . '/diagnoses/' . $diagnose . '.php');
		if (in_array('Aitsu_Core_Diagnose_Diagnose_Interface', class_implements($diagnose))) {
			return call_user_func_array(array (
				self :: _getClassName($diagnose),
				'check'
			), array (
				$step
			));
		}

		return null;
	}
}
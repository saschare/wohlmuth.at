<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: BackendRequest.php 19481 2010-10-21 15:15:07Z akm $}
 */

class Aitsu_Event_BackendRequest extends Aitsu_Event_Abstract {

	public static function raise($signature, $request) {

		$module = $request->getParam('module');
		$module = empty ($module) ? 'default' : $module;
		$controller = $request->getParam('controller');
		$action = $request->getParam('action');

		$signature = array (
			'backend',
			$signature,
			$module,
			$controller,
			$action
		);

		if ($module == 'default' && $controller == 'plugin') {
			$signature[] = $request->getParam('plugin');
			$signature[] = $request->getParam('paction');
		}

		new self(implode('.', $signature), array (
			'request' => $request
		));
	}
}
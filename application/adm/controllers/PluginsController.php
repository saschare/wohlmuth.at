<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: PluginsController.php 19010 2010-09-27 13:17:36Z akm $}
 */

class PluginsController extends Zend_Controller_Action {

	public function init() {

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'plugins'
			))) {
			throw new Exception('Access denied');
		}
	}

	public function indexAction() {

		$user = Aitsu_Adm_User :: getInstance();

		$dir = APPLICATION_PATH . '/plugins/generic';
		$this->view->area = $this->getRequest()->getParam('area');

		$files = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/plugins/generic', '.description.txt');
		sort($files);
		$baseLength = strlen(APPLICATION_PATH . '/plugins/generic/');
		$this->view->plugins = array ();
		foreach ($files as $file) {
			$content = explode("\n", file_get_contents($file));
			$file = substr($file, $baseLength);
			$file = explode('/', $file);
			if (count($file) == 2) {
				if ($user != null && $user->isAllowed(array (
						'area' => 'plugin.' . $file[0]
					))) {
					$this->view->plugins[$file[0]] = (object) array (
						'plugins' => array (),
						'name' => trim($content[0]),
						'description' => trim($content[1])
					);
				}
			}
			elseif (count($file) == 3) {
				if ($user != null && $user->isAllowed(array (
						'area' => 'plugin.' . $file[0] . '.' . $file[1]
					))) {
					$this->view->plugins[$file[0]]->plugins[] = (object) array (
						'plugin' => $file[1],
						'name' => trim($content[0]),
						'description' => trim($content[1]),
						'area' => $file[0]
					);
				}
			}
		}
	}

}
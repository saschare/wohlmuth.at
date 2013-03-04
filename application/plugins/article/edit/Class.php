<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class EditArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4ca6ea6a-3cbc-4f64-a323-0e9f7f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'edit',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Edit'),
			'enabled' => self :: getPosition($idart, 'edit'),
			'position' => self :: getPosition($idart, 'edit'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->pluginId = self :: ID;
		$idart = $this->getRequest()->getParam('idart');

		$idartlang = Aitsu_Persistence_Article :: factory($idart)->load()->idartlang;
		$this->view->idart = $idart;
		$this->view->idartlang = $idartlang;
	}

	public function editcontentAction() {

		$type = $this->getRequest()->getParam('type');
		$container = $this->getRequest()->getParam('container');
		$idartlang = $this->getRequest()->getParam('idartlang');
		$params = str_replace('\n', "\n", $this->getRequest()->getParam('params'));

		Aitsu_Content_Edit :: start($type . '-' . $container);
		$module = Moraso_Module :: factory($idartlang, $container, null, $type);
		$module->getOutput(false, '0', $container, $params);
		$help = $module->getHelp();
		Aitsu_Content_Edit :: end();

		$editInfo = Aitsu_Content_Edit :: getContents();
		$configs = Aitsu_Content_Edit :: getConfigs();	
		$configInfo = array ();
		$configTabs = array();
		foreach ($configs as $config) {
			if (isset($config->tab) && $config->tab) {
				$configTabs[] = $config;
			} else {
				$fieldset = $config->fieldset == '' ? -1 : $config->fieldset;
				$configInfo[$fieldset][] = $config;
			}
		}

		foreach ($editInfo as $key => $panel) {
			$editInfo[$key]->content = Aitsu_Content :: get($panel->index, $panel->type, $panel->idart, $panel->idlang, null);
		}
		$this->view->data = (object) array (
			'type' => $type,
			'idartlang' => $idartlang,
			'container' => $container,
			'params' => $params,
			'editInfo' => $editInfo,
			'configTabs' => $configTabs,
			'configInfo' => $configInfo,
			'help' => $help
		);

	}

	public function saveAction() {

		$this->_helper->layout->disableLayout();

		$type = $this->getRequest()->getParam('type');
		$container = $this->getRequest()->getParam('container');
		$idartlang = $this->getRequest()->getParam('idartlang');
		$params = $this->getRequest()->getParam('params');
		$index = $this->getRequest()->getParam('index');
		$contents = $this->getRequest()->getParam('content');

		$config = array ();
		$configType = array ();

		foreach ($_REQUEST as $key => $value) {
			if (substr($key, 0, 11) == 'configType-') {
				$configType[substr($key, 11)] = $value;
			}
			elseif (substr($key, 0, 7) == 'config-') {
				$config[substr($key, 7)] = $value;
			}
		}

		if (isset($_REQUEST['json'])) {
			$json = json_decode($_REQUEST['json']);
			foreach ($json as $key => $value) {
				$config[$key] = $value;
			}
		}
		
		$this->_restoreContext($idartlang);

		Aitsu_Registry :: get()->env->ajaxResponse = '1';
		Aitsu_Registry :: get()->env->editAction = '1';
		Aitsu_Registry :: get()->env->edit = '1';
		Aitsu_Registry :: get()->env->env = 'backend';

		try {
			Aitsu_Db :: startTransaction();

			Aitsu_Event :: raise('backend.article.edit.save.start', array (
				'idartlang' => $idartlang
			));

			if ($contents != null) {
				foreach ($contents as $key => $value) {
					Aitsu_Content :: set($key, $idartlang, $value);
				}
			}

			foreach ($configType as $key => $value) {
				$cType = $value;
				$fieldValue = isset($config[$key]) ? $config[$key] : null;					
				Aitsu_Core_Article_Property :: factory($idartlang)->setValue('ModuleConfig_' . $container, $key, $fieldValue, $cType);
			}

			Aitsu_Registry :: get()->env->substituteEmptyAreas = '1';

			$output = Moraso_Module :: factory($idartlang, $container, null, $type)->getOutput(true, '1', $container, $params);

			if (Aitsu_Content_Edit :: noEdit($type)) {
				$data = $output;
			} else {
				if (Aitsu_Content_Edit :: isBlock()) {
					$data = '<div><div class="aitsu_hover">' . $output . '</div></div>';
				} else {
					$data = '<span><span class="aitsu_hover">' . $output . '</span></span>';
				}
			}

			Aitsu_Event :: raise('backend.article.edit.save.end', array (
				'idartlang' => $idartlang
			));

			Aitsu_Persistence_Article :: touch($idartlang);

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}

		$this->_helper->json((object) array (
			'success' => true,
			'data' => (object) array (
				'html' => $data,
				'type' => $type,
				'container' => $container,
				'idartlang' => $idartlang
			)
		));
	}

	protected function _restoreContext($idartlang) {

		/*
		 * To render code as it would be done in the frontend we have
		 * to restore the context.
		 */
		$article = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	artlang.idart as idart, ' .
		'	artlang.idlang as idlang, ' .
		'	artlang.idartlang as idartlang, ' .
		'	lang.idclient as idclient ' .
		'from _art_lang as artlang ' .
		'left join _lang as lang on artlang.idlang = lang.idlang ' .
		'where idartlang = :idartlang ', array (
			':idartlang' => $idartlang
		));

		if ($article) {
			Aitsu_Registry :: get()->env->idart = $article['idart'];
			Aitsu_Registry :: get()->env->idartlang = $article['idartlang'];
			Aitsu_Registry :: get()->env->idlang = $article['idlang'];
			Aitsu_Registry :: get()->env->lang = $article['idlang'];
			Aitsu_Registry :: get()->env->client = $article['idclient'];
			Aitsu_Registry :: get()->env->idclient = $article['idclient'];
		}
	}
}
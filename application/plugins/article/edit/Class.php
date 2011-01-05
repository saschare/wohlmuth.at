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
		Aitsu_Core_Module :: factory($idartlang, $container, null, $type)->getOutput(false, '0', $container, $params);
		Aitsu_Content_Edit :: end();

		$editInfo = Aitsu_Content_Edit :: getContents();
		$configInfo = Aitsu_Content_Edit :: getConfigs();

		foreach ($editInfo as $key => $panel) {
			$editInfo[$key]->content = Aitsu_Content :: get($panel->index, $panel->type, $panel->idart, $panel->idlang, null);
		}
var_dump($editInfo);exit;
		$this->view->type = $type;
		$this->view->idartlang = $idartlang;
		$this->view->container = $container;
		$this->view->params = $params;
		$this->view->editInfo = $editInfo;
		$this->view->configInfo = $configInfo;
	}

	public function saveAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$type = $this->getRequest()->getParam('type');
		$container = $this->getRequest()->getParam('container');
		$idartlang = $this->getRequest()->getParam('idartlang');
		$params = $this->getRequest()->getParam('params');

		$this->_restoreContext($idartlang);

		Aitsu_Registry :: get()->env->ajaxResponse = '1';
		Aitsu_Registry :: get()->env->editAction = '1';
		Aitsu_Registry :: get()->env->edit = '1';
		Aitsu_Registry :: get()->env->env = 'backend';

		$index = $this->getRequest()->getParam('index');
		$contents = $this->getRequest()->getParam('content');
		$config = $this->getRequest()->getParam('config');
		$configType = $this->getRequest()->getParam('configType');

		try {
			Aitsu_Db :: startTransaction();

			Aitsu_Event :: raise('backend.article.edit.save.start', array (
				'idartlang' => $idartlang
			));

			if ($type == 'container') {
				/*
				 * Save standard module data.
				 */
				if ($index != null) {
					foreach ($index as $key => $value) {
						$content = $contents[$key];
						Aitsu_Content :: set($index[$key], $idartlang, $content);
					}
				}

				if ($config != null) {
					foreach ($config as $key => $value) {
						$cType = $configType[$key];
						Aitsu_Core_Article_Property :: factory($idartlang)->setValue('ModuleConfig_' . $container, $key, $value, $cType);
					}
				}

				Aitsu_Registry :: get()->env->substituteEmptyAreas = '1';
				echo Aitsu_Core_Module :: factory($idartlang, $container)->getOutput(true, '1');
				return;
			}

			/*
			 * Save shortCode data.
			 */
			if ($index != null) {
				foreach ($index as $key => $value) {
					$content = $contents[$key];
					Aitsu_Content :: set($index[$key], $idartlang, $content);
				}
			}

			if ($config != null) {
				foreach ($config as $key => $value) {
					$cType = $configType[$key];
					Aitsu_Core_Article_Property :: factory($idartlang)->setValue('ModuleConfig_' . $container, $key, $value, $cType);
				}
			}

			Aitsu_Registry :: get()->env->substituteEmptyAreas = '1';

			$output = Aitsu_Core_Module :: factory($idartlang, $container, null, $type)->getOutput(true, '1', $container, $params);

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

		echo $data;
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
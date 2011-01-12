<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class RedirectorArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cc155c9-18c0-42eb-97b9-0ab77f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'redirector',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Redirect'),
			'enabled' => self :: getPosition($idart, 'redirector'),
			'position' => self :: getPosition($idart, 'redirector'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/redirector/forms/redirector.ini', 'edit'));
		$form->setAction($this->view->url());

		$data = Aitsu_Persistence_Article :: factory($id)->load();
		$data->redirect = 1;

		if ($this->getRequest()->isPost()) {
			$data->redirect_url = $_POST['redirect_url'];
		} else {
			$form->setValues($data->toArray());
		}

		$this->view->pluginId = self :: ID;
		$this->view->form = $form;

		if (substr($data->redirect_url, 0, 5) == 'idart') {
			preg_match('/\\d*$/', $data->redirect_url, $match);
			$this->view->targetId = 'idart-' . $match[0];

			$cats = Aitsu_Db :: fetchCol('' .
			'select parent.idcat ' .
			'from _cat_art as catart ' .
			'left join _cat as child on catart.idcat = child.idcat ' .
			'left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
			'where catart.idart = :idart ' .
			'order by parent.lft asc', array (
				':idart' => $match[0]
			));
			$this->view->openCats = "'" . implode("', '", $cats) . "'";
		}
		elseif (substr($data->redirect_url, 0, 5) == 'idcat') {
			preg_match('/\\d*$/', $data->redirect_url, $match);
			$this->view->targetId = 'cat-' . $match[0];

			$cats = Aitsu_Db :: fetchCol('' .
			'select parent.idcat ' .
			'from _cat as child ' .
			'left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
			'where child.idcat = :idcat ' .
			'order by parent.lft asc', array (
				':idcat' => $match[0]
			));
			$this->view->openCats = "'" . implode("', '", $cats) . "'";
		} else {
			$this->view->targetId = '0';
			$this->view->openCats = "'0'";
		}

		if ($this->getRequest()->getParam('loader')) {
			return;
		}

		if (!$form->isValid($_POST)) {
			$this->_helper->json((object) array (
				'status' => 'validationfailure',
				'message' => $this->view->render('index.phtml')
			));
		}

		try {
			$data->setValues($form->getValues())->save();
			$form->setValues($data->toArray());
			$this->_helper->json((object) array (
				'status' => 'success',
				'message' => Zend_Registry :: get('Zend_Translate')->translate('Properties saved.'),
				'data' => (object) $data->toArray(),
				'html' => $this->view->render('index.phtml')
			));
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'status' => 'exception',
				'message' => $e->getMessage()
			));
		}
	}

}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class TranslationController extends Zend_Controller_Action {

	/**
	 * @since 2.1.0.0 - 28.12.2010
	 */
	public function init() {

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'translation'
			))) {
			throw new Exception('Access denied');
		}

		$this->_helper->layout->disableLayout();

		$this->_filter = Aitsu_Util_ExtJs :: encodeFilters($this->getRequest()->getParam('filter'));
	}

	/**
	 * @since 2.1.0.0 - 28.12.2010
	 */
	public function indexAction() {
	}

	public function storeAction() {

		$refresh = $this->getRequest()->getParam('refresh');

		if ($refresh == 1) {
			Aitsu_Translate :: populate(Aitsu_Registry :: get()->session->currentLanguage);
		}

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Translate :: getStore(100, 0, $this->_filter)
		));
	}

	public function editAction() {

		$this->_helper->layout->disableLayout();

		$id = $this->getRequest()->getParam('translationid');

		$form = Aitsu_Forms :: factory('edittranslation', APPLICATION_PATH . '/adm/forms/translation/translation.ini');
		$form->title = Aitsu_Translate :: translate('Edit translation');
		$form->url = $this->view->url();

		$data = Aitsu_Persistence_Translate :: factory($id)->load()->toArray();
		$form->setValues($data);

		if (!$this->getRequest()->isPost()) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				$values = $form->getValues();

				/*
				 * Update config set.
				 */
				Aitsu_Persistence_Translate :: factory($id)->load()->setValues($values)->save();

				$this->_helper->json((object) array (
					'success' => true
				));
			} else {
				$this->_helper->json((object) array (
					'success' => false,
					'errors' => $form->getErrors()
				));
			}
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'exception' => true,
				'message' => $e->getMessage()
			));
		}

		/*$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$id = $this->getRequest()->getParam('id') == null ? $this->getRequest()->getParam('translationid') : $this->getRequest()->getParam('id');
		
		if ($this->getRequest()->getParam('cancel') != 1) {
		
			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/translation/translation.ini', 'edit'));
			$form->setAction($this->view->url());
		
			if (!$this->getRequest()->isPost()) {
				$form->setValues(Aitsu_Persistence_Translate :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}
		
			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('translation/newtranslation.phtml');
				return;
			}
		
			$values = $form->getValues();
		
			Aitsu_Persistence_Translate :: factory()->setValues($values)->save();
		} // else: form has been cancelled.
		
		$this->view->translations = Aitsu_Persistence_Translate :: getByLanguage(Aitsu_Registry :: get()->session->currentLanguage);
		
		echo $this->view->render('translation/translationlist.phtml');*/
	}

	/**
	 * @since 2.1.0.0 - 28.12.2010
	 */
	public function deleteAction() {

		$this->_helper->layout->disableLayout();

		Aitsu_Persistence_Translate :: factory($this->getRequest()->getParam('translationid'))->remove();

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	/**
	 * @todo Implement export into version 2.1.x
	 */
	public function exportAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = 'translation.';
		$filename .= Aitsu_Registry :: get()->session->currentClient . '.';
		$filename .= Aitsu_Registry :: get()->session->currentLanguage . '.';
		$filename .= date('Y-m-d-H-i-s') . '.xml';

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		echo Aitsu_Filter_ToXml :: get(array (
			'info' => array (
				'type' => 'translation',
				'date' => date('Y-m-d H:i:s'),
				'client' => Aitsu_Registry :: get()->session->currentClient,
				'language' => array (
					'id' => Aitsu_Registry :: get()->session->currentLanguage,
					'locale' => Aitsu_Persistence_Language :: factory(Aitsu_Registry :: get()->session->currentLanguage)->load()->locale
				),
				'system' => array (
					'HTTP_HOST' => $_SERVER['HTTP_HOST'],
					'SERVER_NAME' => $_SERVER['SERVER_NAME'],
					'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
					'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
				)
			),
			'data' => Aitsu_Persistence_Translate :: getByLanguage(Aitsu_Registry :: get()->session->currentLanguage)
		))->saveXML();
	}
}
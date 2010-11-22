<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: TranslationController.php 18801 2010-09-16 21:20:02Z akm $}
 */

class TranslationController extends Zend_Controller_Action {

	public function init() {

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'translation'
			))) {
			throw new Exception('Access denied');
		}
	}

	public function indexAction() {

		$this->view->translations = Aitsu_Persistence_Translate :: getByLanguage(Aitsu_Registry :: get()->session->currentLanguage);
	}

	public function refreshAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		try {
			Aitsu_Translate :: populate(Aitsu_Registry :: get()->session->currentLanguage);
		} catch (Exception $e) {
			echo '<pre>' . $e->getTraceAsString() . '</pre>';
		}

		$this->view->translations = Aitsu_Persistence_Translate :: getByLanguage(Aitsu_Registry :: get()->session->currentLanguage);

		echo $this->view->render('translation/translationlist.phtml');
	}

	public function filtertranslationAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$term = $this->getRequest()->getParam('filter-translation');

		$this->view->translations = Aitsu_Persistence_Translate :: getByLanguage(Aitsu_Registry :: get()->session->currentLanguage, $term);
		$this->view->filterterm = $this->getRequest()->getParam('filter-user');

		echo $this->view->render('translation/translationlist.phtml');
	}

	public function edittranslateAction() {

		$this->_helper->layout->disableLayout();
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

		echo $this->view->render('translation/translationlist.phtml');
	}
	
	public function deletetranslateAction() {
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$id = $this->getRequest()->getParam('id');
		
		if ($id != null) {
			Aitsu_Persistence_Translate :: factory($id)->remove();
		}
		
		$this->view->translations = Aitsu_Persistence_Translate :: getByLanguage(Aitsu_Registry :: get()->session->currentLanguage);

		echo $this->view->render('translation/translationlist.phtml');
	}

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
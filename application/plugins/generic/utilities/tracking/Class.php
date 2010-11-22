<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 18915 2010-09-21 10:41:39Z akm $}
 */

class TrackingPluginController extends Aitsu_Adm_Plugin_Controller {

	protected $_pluginPath;

	public function init() {

		$this->_pluginPath = dirname(__FILE__);

		include_once ($this->_pluginPath . '/lib/Customer.php');
		include_once ($this->_pluginPath . '/lib/Type.php');
		include_once ($this->_pluginPath . '/lib/Project.php');
		include_once ($this->_pluginPath . '/lib/Track.php');
	}

	public function indexAction() {

		$this->view->customers = Plugin_Tracking_Customer :: getByIdentifier();
		$this->view->trackingtypes = Plugin_Tracking_Type :: getAll();
		$this->view->projects = Plugin_Tracking_Project :: getByIdentifier();
		$this->view->tracks = Plugin_Tracking_Track :: getCurrent();
	}

	public function listcustomersAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filter = $this->getRequest()->getParam('filter-customer');
		$this->view->customers = Plugin_Tracking_Customer :: getByIdentifier('%' . $filter . '%');
		echo $this->view->render('customerlist.phtml');
	}
	
	public function listprojectsAction() {
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filter = $this->getRequest()->getParam('filter-project');
		$this->view->projects = Plugin_Tracking_Project :: getByIdentifier('%' . $filter . '%');
		echo $this->view->render('projectlist.phtml');		
	}

	public function listtracksAction() {
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filter = $this->getRequest()->getParam('filter-track');
		$this->view->tracks = Plugin_Tracking_Track :: getCurrent('%' . $filter . '%');
		echo $this->view->render('tracklist.phtml');		
	}

	public function newcustomerAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/customer.ini', 'new'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Customer :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->customers = Plugin_Tracking_Customer :: getByIdentifier();
		echo $this->view->render('customerlist.phtml');
	}

	public function newprojectAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/project.ini', 'new'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Project :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->projects = Plugin_Tracking_Project :: getByIdentifier();
		echo $this->view->render('projectlist.phtml');
	}

	public function newtrackingtypeAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/trackingtype.ini', 'new'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Type :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->trackingtypes = Plugin_Tracking_Type :: getAll();
		echo $this->view->render('trackingtypelist.phtml');
	}

	public function newtrackAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/track.ini', 'new'));
			$form->setAction($this->view->url());
			
			$form->getElement('typeid')->setMultiOptions(Plugin_Tracking_Type :: getAsArray());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Track :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->tracks = Plugin_Tracking_Track :: getCurrent();
		echo $this->view->render('tracklist.phtml');
	}

	public function deletecustomerAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Plugin_Tracking_Customer :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->customers = Plugin_Tracking_Customer :: getByIdentifier();
		echo $this->view->render('customerlist.phtml');
	}
	
	public function deletetrackAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Plugin_Tracking_Track :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->tracks = Plugin_Tracking_Track :: getCurrent();
		echo $this->view->render('tracklist.phtml');
	}
	
	public function deleteprojectAction() {
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Plugin_Tracking_Project :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->projects = Plugin_Tracking_Project :: getByIdentifier();
		echo $this->view->render('projectlist.phtml');
	}

	public function deletetrackingtypeAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Plugin_Tracking_Type :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->trackingtypes = Plugin_Tracking_Type :: getAll();
		echo $this->view->render('trackingtypelist.phtml');
	}

	public function editcustomerAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/customer.ini', 'edit'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Plugin_Tracking_Customer :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('newcustomer.phtml');
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Customer :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->customers = Plugin_Tracking_Customer :: getByIdentifier();
		echo $this->view->render('customerlist.phtml');
	}

	public function edittrackAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/track.ini', 'edit'));
			$form->setAction($this->view->url());
			
			$form->getElement('typeid')->setMultiOptions(Plugin_Tracking_Type :: getAsArray());

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Plugin_Tracking_Track :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('newtrack.phtml');
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Track :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->tracks = Plugin_Tracking_Track :: getCurrent();
		echo $this->view->render('tracklist.phtml');
	}

	public function editprojectAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/project.ini', 'edit'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Plugin_Tracking_Project :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('newproject.phtml');
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Project :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->projects = Plugin_Tracking_Project :: getByIdentifier();
		echo $this->view->render('projectlist.phtml');
	}

	public function edittrackingtypeAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		if ($this->getRequest()->getParam('cancel') != 1) {
			$form = new Aitsu_Form(new Zend_Config_Ini($this->_pluginPath . '/forms/trackingtype.ini', 'edit'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Plugin_Tracking_Type :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('newtrackingtype.phtml');
				return;
			}

			$values = $form->getValues();

			Plugin_Tracking_Type :: factory()->setValues($values)->save();

		} // else: form has been cancelled.

		$this->view->trackingtypes = Plugin_Tracking_Type :: getAll();
		echo $this->view->render('trackingtypelist.phtml');
	}

	public function customerjsonAction() {

		$return = array ();

		$term = $this->getRequest()->getParam('term');
		$customers = Plugin_Tracking_Customer :: getByIdentifier('%' . $term . '%');

		foreach ($customers as $customer) {
			$return[] = (object) array (
				'id' => $customer->customerid,
				'label' => $customer->identifier,
				'value' => $customer->identifier
			);
		}

		$this->_helper->json($return);
	}

	public function projectjsonAction() {

		$return = array ();

		$term = $this->getRequest()->getParam('term');
		$projects = Plugin_Tracking_Project :: getCurrentByIdentifier('%' . $term . '%');

		foreach ($projects as $project) {
			$return[] = (object) array (
				'id' => $project->projectid,
				'label' => $project->identifier,
				'value' => $project->identifier
			);
		}

		$this->_helper->json($return);
	}

	public function showtrackAction() {
		
		$this->_helper->layout->disableLayout();
	}
}
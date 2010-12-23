<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class AclController extends Zend_Controller_Action {

	public function init() {

		if ($this->getRequest()->getActionName() == 'login' || $this->getRequest()->getActionName() == 'logout') {
			return;
		}

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'usermanagement',
				'action' => 'crud'
			)) && !Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'usermanagement',
				'action' => $this->getRequest()->getActionName()
			))) {
			throw new Exception('Access denied');
		}
	}

	public function indexAction() {

		$this->_helper->layout->disableLayout();

		$this->view->users = Aitsu_Persistence_User :: getByName();
		$this->view->privileges = Aitsu_Persistence_Privilege :: getAll();
		$this->view->roles = Aitsu_Persistence_Role :: getAll();
		$this->view->resources = Aitsu_Persistence_Resource :: getAll();
	}

	public function loginAction() {

		if (Aitsu_Config :: equals('extjstest', true)) {
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$this->render('loginextjs');
			return;
		}

		$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/acl/login.ini', 'new'));
		$form->setAction($this->view->url());

		if ($this->getRequest()->isPost()) {
			$form->setValues(array (
				'login' => $this->getRequest()->getParam('login')
			));
		}

		$this->view->form = $form;
	}

	/**
	 * User profile update.
	 * @since 2.1.0.0 - 22.12.2010
	 */
	public function profilAction() {

		$this->_helper->layout->disableLayout();

		$form = Aitsu_Forms :: factory('userprofile', APPLICATION_PATH . '/adm/forms/acl/userprofile.ini');
		$form->title = Aitsu_Translate :: translate('User profile');
		$form->url = $this->view->url();

		$id = Aitsu_Adm_User :: getInstance()->getId();
		$form->setValues(Aitsu_Persistence_User :: factory($id)->load()->toArray());
		$form->setValue('password', null);

		$langs = array ();
		foreach (Aitsu_Persistence_Language :: getAsArray() as $key => $value) {
			$langs[] = (object) array (
				'value' => $key,
				'name' => $value
			);
		}
		$form->setOptions('idlang', $langs);

		if (!$this->getRequest()->isPost()) {
			$this->view->form = $form;
			return;
		}

		try {
			if ($form->isValid()) {
				/*
				 * Persist the data.
				 */
				$values = $form->getValues();
				if (empty ($values['password'])) {
					unset ($values['password']);
				} else {
					$values['password'] = md5($values['password']);
				}
				Aitsu_Persistence_User :: factory($id)->load()->setValues($values)->save();
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

	}

	public function logoutAction() {

		Zend_Session :: destroy();

		$this->_redirect('/');
	}

	/**
	 * Updates existing or inserts new users.
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function edituserAction() {

		$this->_helper->layout->disableLayout();

		$id = $this->getRequest()->getParam('userid');

		$form = Aitsu_Forms :: factory('edituser', APPLICATION_PATH . '/adm/forms/acl/user.ini');
		$form->title = Aitsu_Translate :: translate('Edit user');
		$form->url = $this->view->url();

		$roles = array ();
		foreach (Aitsu_Persistence_Role :: getAsArray() as $key => $value) {
			$roles[] = (object) array (
				'value' => $key,
				'name' => $value
			);
		}
		$form->setOptions('roles', $roles);

		if (!empty ($id)) {
			$userData = Aitsu_Persistence_User :: factory($id)->load()->toArray();
			$form->setValues($userData);
			$form->setValue('password', null);
		}

		if (!$this->getRequest()->isPost()) {
			$this->view->form = $form;
			return;
		}

		try {
			if ($form->isValid()) {
				$values = $form->getValues();

				/*
				 * Additionally we have to make sure, the login name is not already 
				 * in use.
				 */
				if (!Aitsu_Persistence_User :: isLoginUnique($id, $values['login'])) {
					$this->_helper->json((object) array (
						'success' => false,
						'errors' => array (
							(object) array (
								'id' => 'login',
								'msg' => Aitsu_Translate :: translate('The login is already in use.')
							)
						)
					));
				}

				/*
				 * Persist the data.
				 */
				if (empty ($values['password'])) {
					unset ($values['password']);
				} else {
					$values['password'] = md5($values['password']);
				}
				$values['acfrom'] = empty ($values['acfrom']) ? date('Y-m-d H:i:s') : $values['acfrom'];
				$values['acuntil'] = empty ($values['acuntil']) ? date('Y-m-d H:i:s', time() + 365 * 24 * 60 * 60) : $values['acfrom'];

				if (empty ($id)) {
					/*
					 * New user.
					 */
					unset ($values['userid']);
					Aitsu_Persistence_User :: factory()->setValues($values)->save();
				} else {
					/*
					 * Update user.
					 */
					Aitsu_Persistence_User :: factory($id)->load()->setValues($values)->save();
				}

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
	}

	public function filteruserAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$term = $this->getRequest()->getParam('filter-user');
		$term .= '%';

		$this->view->users = Aitsu_Persistence_User :: getByName($term);
		$this->view->filterterm = $this->getRequest()->getParam('filter-user');

		echo $this->view->render('acl/userlist.phtml');
	}

	public function deleteuserAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Aitsu_Persistence_User :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->users = Aitsu_Persistence_User :: getByName();
		echo $this->view->render('acl/userlist.phtml');
	}

	public function deleteresourceAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Aitsu_Persistence_Resource :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->resources = Aitsu_Persistence_Resource :: getAll();
		echo $this->view->render('acl/resourcelist.phtml');
	}

	public function newroleAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/acl/role.ini', 'new'));
			$form->setAction($this->view->url());

			$form->getElement('privileges')->setMultiOptions(Aitsu_Persistence_Privilege :: getAsArray());
			$form->getElement('clients')->setMultiOptions(Aitsu_Persistence_Clients :: getAsArray());
			$form->getElement('languages')->setMultiOptions(Aitsu_Persistence_Language :: getAsArray());
			$form->getElement('resources')->setMultiOptions(Aitsu_Persistence_Resource :: getAsArray());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Role :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->roles = Aitsu_Persistence_Role :: getAll();

		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->render('acl/rolelist.phtml');
	}

	public function editroleAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam('id') == null ? $this->getRequest()->getParam('roleid') : $this->getRequest()->getParam('id');

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/acl/role.ini', 'edit'));
			$form->setAction($this->view->url());

			$form->getElement('identifier')->getValidator('unique')->setId($id);
			$form->getElement('privileges')->setMultiOptions(Aitsu_Persistence_Privilege :: getAsArray());
			$form->getElement('clients')->setMultiOptions(Aitsu_Persistence_Clients :: getAsArray());
			$form->getElement('languages')->setMultiOptions(Aitsu_Persistence_Language :: getAsArray());
			$form->getElement('resources')->setMultiOptions(Aitsu_Persistence_Resource :: getAsArray());

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Aitsu_Persistence_Role :: factory($id)->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('acl/newrole.phtml');
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Role :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->roles = Aitsu_Persistence_Role :: getAll();

		echo $this->view->render('acl/rolelist.phtml');
	}

	public function deleteroleAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Aitsu_Persistence_Role :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->roles = Aitsu_Persistence_Role :: getAll();
		echo $this->view->render('acl/rolelist.phtml');
	}

	public function newprivilegeAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/acl/privilege.ini', 'new'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Privilege :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->privileges = Aitsu_Persistence_Privilege :: getAll();

		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->render('acl/privilegelist.phtml');
	}

	public function newresourceAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/acl/resource.ini', 'new'));
			$form->setAction($this->view->url());

			$this->view->openCats = null;
			$this->view->targetId = null;

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Resource :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->resources = Aitsu_Persistence_Resource :: getAll();

		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->render('acl/resourcelist.phtml');
	}

	public function editprivilegeAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam('id') == null ? $this->getRequest()->getParam('privilegeid') : $this->getRequest()->getParam('id');

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/acl/privilege.ini', 'edit'));
			$form->setAction($this->view->url());

			$form->getElement('identifier')->getValidator('unique')->setId($id);

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Aitsu_Persistence_Privilege :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('acl/newprivilege.phtml');
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Privilege :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->privileges = Aitsu_Persistence_Privilege :: getAll();

		echo $this->view->render('acl/privilegelist.phtml');
	}

	public function editresourceAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam('id') == null ? $this->getRequest()->getParam('resourceid') : $this->getRequest()->getParam('id');

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/acl/resource.ini', 'edit'));
			$form->setAction($this->view->url());

			$res = Aitsu_Persistence_Resource :: factory($this->getRequest()->getParam('id'))->load();

			if ($res->resourcetype == 'art') {
				$this->view->targetId = 'idart-' . $res->identifier;
				$cats = Aitsu_Db :: fetchCol('' .
				'select parent.idcat ' .
				'from _cat_art as catart ' .
				'left join _cat as child on catart.idcat = child.idcat ' .
				'left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
				'where catart.idart = :idart ' .
				'order by parent.lft asc', array (
					':idart' => $res->identifier
				));
				$this->view->openCats = "'" . implode("', '", $cats) . "'";
			}
			elseif ($res->resourcetype == 'cat') {
				$this->view->targetId = 'cat-' . $res->identifier;
				$cats = Aitsu_Db :: fetchCol('' .
				'select parent.idcat ' .
				'from _cat as child ' .
				'left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
				'where child.idcat = :idcat ' .
				'order by parent.lft asc', array (
					':idcat' => $res->identifier
				));
				$this->view->openCats = "'" . implode("', '", $cats) . "'";
			} else {
				$this->view->targetId = null;
			}

			if (!$this->getRequest()->isPost()) {
				$form->setValues($res->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('acl/newresource.phtml');
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Resource :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->resources = Aitsu_Persistence_Resource :: getAll();

		echo $this->view->render('acl/resourcelist.phtml');
	}

	public function deleteprivilegeAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Aitsu_Persistence_Privilege :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->privileges = Aitsu_Persistence_Privilege :: getAll();

		echo $this->view->render('acl/privilegelist.phtml');
	}

	public function exportusersAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = 'users.';
		$filename .= date('Y-m-d-H-i-s') . '.xml';

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		echo Aitsu_Filter_ToXml :: get(array (
			'info' => array (
				'type' => 'users',
				'date' => date('Y-m-d H:i:s'),
				'system' => array (
					'HTTP_HOST' => $_SERVER['HTTP_HOST'],
					'SERVER_NAME' => $_SERVER['SERVER_NAME'],
					'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
					'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
				)
			),
			'data' => Aitsu_Persistence_User :: getUsersWithRoles()
		))->saveXML();
	}

	public function exportrolesAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = 'roles.';
		$filename .= date('Y-m-d-H-i-s') . '.xml';

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		echo Aitsu_Filter_ToXml :: get(array (
			'info' => array (
				'type' => 'roles',
				'date' => date('Y-m-d H:i:s'),
				'system' => array (
					'HTTP_HOST' => $_SERVER['HTTP_HOST'],
					'SERVER_NAME' => $_SERVER['SERVER_NAME'],
					'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
					'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
				)
			),
			'data' => Aitsu_Persistence_Role :: getFullRoles()
		))->saveXML();
	}

	public function exportresourcesAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = 'resource.';
		$filename .= date('Y-m-d-H-i-s') . '.xml';

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		echo Aitsu_Filter_ToXml :: get(array (
			'info' => array (
				'type' => 'resources',
				'date' => date('Y-m-d H:i:s'),
				'system' => array (
					'HTTP_HOST' => $_SERVER['HTTP_HOST'],
					'SERVER_NAME' => $_SERVER['SERVER_NAME'],
					'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
					'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
				)
			),
			'data' => Aitsu_Persistence_Resource :: getAll()
		))->saveXML();
	}

	public function exportprivilegesAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = 'privileges.';
		$filename .= date('Y-m-d-H-i-s') . '.xml';

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		echo Aitsu_Filter_ToXml :: get(array (
			'info' => array (
				'type' => 'privileges',
				'date' => date('Y-m-d H:i:s'),
				'system' => array (
					'HTTP_HOST' => $_SERVER['HTTP_HOST'],
					'SERVER_NAME' => $_SERVER['SERVER_NAME'],
					'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
					'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
				)
			),
			'data' => Aitsu_Persistence_Privilege :: getAll()
		))->saveXML();
	}

	public function exportAction() {

		$this->_helper->viewRenderer->setNoRender(true);

		$this->view->users = Aitsu_Persistence_User :: getByName();
		$this->view->privileges = Aitsu_Persistence_Privilege :: getAll();
		$this->view->roles = Aitsu_Persistence_Role :: getAll();
		$this->view->resources = Aitsu_Persistence_Resource :: getAll();

		echo $this->view->render('acl/index.phtml');
	}

	public function refreshsessionAction() {

		$this->_helper->json((object) array (
			'success' => Aitsu_Adm_User :: getInstance() != null,
			'time' => date('Y-m-d H:i:s')
		));
	}

}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class ShopitemArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4dca4629-e274-4c73-8d76-0d207f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'shopitem',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Shop item'),
			'enabled' => false, //self :: getPosition($idart, 'shopitem'),
			'position' => self :: getPosition($idart, 'shopitem'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = Aitsu_Forms :: factory('shopitem', APPLICATION_PATH . '/plugins/article/shopitem/forms/item.ini');
		$form->title = Aitsu_Translate :: translate('Shop item');
		$form->url = $this->view->url(array (
			'plugin' => 'shopitem',
			'paction' => 'index'
		), 'aplugin');

		$data = Wdrei_Shop_Persistence_Product :: factory($id)->load();
		$form->setValues(array_merge($data->toArray(), array (
			'idart' => $id
		)));

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	classid, ' .
		'	identifier ' .
		'from _shop_tax_class ' .
		'order by ' .
		'	identifier asc');
		foreach ($results as $tax) {
			$taxes[] = (object) array (
				'name' => $tax['identifier'],
				'value' => $tax['classid']
			);
		}
		$form->setOptions('classid', $taxes);

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	currencyid, ' .
		'	code ' .
		'from _shop_currency ' .
		'order by ' .
		'	code asc');
		foreach ($results as $currency) {
			$currencies[] = (object) array (
				'name' => $currency['code'],
				'value' => $currency['currencyid']
			);
		}
		$form->setOptions('currencyid', $currencies);

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				/*
				 * Persist the data.
				 */
				$data->setValues($form->getValues())->save();

				$this->_helper->json((object) array (
					'success' => true,
					'data' => (object) $data->toArray()
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

}
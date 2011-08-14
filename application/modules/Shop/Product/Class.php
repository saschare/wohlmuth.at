<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Shop_Product_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Shop.Product', true);

		$output = '';
		if ($instance->_get('ShopProduct', $output)) {
			return $output;
		}

		$template = empty ($this->_params->template) ? 'index' : $this->_params->template;

		$view = $this->_getView();
		$view->product = Wdrei_Shop_Persistence_Product :: factory(Aitsu_Registry :: get()->env->idart)->load();

		$output = $view->render($template . '.phtml');
		$this->_save($output, 'eternal');

		return $output;
	}
}
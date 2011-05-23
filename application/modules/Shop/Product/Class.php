<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_Shop_Product_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('Shop.Product', true);

		$instance = new self();
		
		$output = '';
		if ($instance->_get('ShopProduct' . $context['index'], $output)) {
			return $output;
		}

		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		
		$template = empty($params->template) ? 'index' : $params->template;
		
		$view = $instance->_getView();
		$view->product = Wdrei_Shop_Persistence_Product :: factory(Aitsu_Registry :: get()->env->idart)->load();
		
		$output = $view->render($template . '.phtml');
		$instance->_save($output, 'eternal');
		return $output;
	}
}
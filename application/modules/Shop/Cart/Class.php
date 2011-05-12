<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_Shop_Cart_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('Shop.Cart', true);

		$instance = new self();

		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$template = empty ($params->template) ? 'index' : $params->template;

		$view = $instance->_getView();
		$view->order = Wdrei_Shop_Persistence_Order :: factory()->load();

		if (isset ($_POST['updateorder'])) {
			if ($_POST['updateorder']['action'] == 'add') {
				/*
				 * Add the specified product to the cart.
				 */
				$view->order->addItem($_POST['updateorder']['productid'], $_POST['updateorder']['amount'], $_POST['updateorder']['price'], $_POST['updateorder']['additionalinfo']);
			}
			elseif ($_POST['updateorder']['action'] == 'remove') {
				/*
				 * Remove an item from the cart.
				 */
				$view->order->removeItem($_POST['updateorder']['itemid']);
			}
			elseif ($_POST['updateorder']['action'] == 'update') {
				/*
				 * Update an item of the cart.
				 */
				$view->order->updateItem($_POST['updateorder']['itemid'], $_POST['updateorder']['amount'], $_POST['updateorder']['price'], $_POST['updateorder']['additionalinfo']);
			}
			$view->order->load();
		}

		$output = $view->render($template . '.phtml');
		$instance->_save($output, 'eternal');
		return $output;
	}
}
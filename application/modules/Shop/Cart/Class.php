<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Shop_Cart_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Shop.Cart', true);

		$template = empty ($this->_params->template) ? 'index' : $this->_params->template;

		$view = $this->_getView();
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
				 * Update items of the cart.
				 */
				foreach ($_POST['updateorder']['item'] as $itemid => $item) {
					$view->order->updateItem($itemid, $item['amount'], $item['price'], $item['additionalinfo']);
				}
			}
			$view->order->load();
		}

		$output = $view->render($template . '.phtml');

		return $output;
	}
}
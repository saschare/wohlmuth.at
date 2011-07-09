<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Example_Class extends Aitsu_Module_Abstract {
	
	/*
	 * Set this value to true or remove the line below to 
	 * allow to edit. 
	 */
	protected $_allowEdit = false;

	protected function _main() {

		/*
		 * 
		 * The abstract module class (Aitsu_Module_Abstract) takes
		 * care to hold the values needed within the module.
		 * 
		 * $this->_index
		 * 		The index used, when the module is referenced. This value
		 * 		is mainly used when configuration is necessary.
		 * 
		 * $this->_params
		 * 		An object containing the parameters set when referenced
		 * 		in script mode (as script with type application/x-aitsu)
		 * 
		 * $this->_getView()
		 * 		Gives you a reference to the view object.
		 * 
		 * The only thing always has to be done is returning a string value
		 * to be used as output. You may use a view to form the output (as
		 * shown below).
		 * 
		 * If you would like the output to be cached, overwrite the method
		 * _cachingPeriod and return the period in seconds.
		 * 
		 */
		 
		/*
		 * Let the abstract class give us a view reference...
		 */
		$view = $this->_getView();
		
		/*
		 * Add values to the context of the view...
		 */
		$view->data = (object) array (
			'index' => $this->_index,
			'params' => $this->_params,
			'currentTimestamp' => time()
		);

		/*
		 * And return the result of the view rendering using the
		 * template index.phtml (or any other of your choice).
		 */
		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 20;
	}
}
<?php


/**
 * View ressource.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: View.php 17695 2010-07-23 11:44:24Z akm $}
 */

class Aitsu_Api_Ressource_View implements Aitsu_Api_Rest_Interface {

	protected $request;
	protected $response;

	protected function __construct($request, $response) {

		$this->request = $request;
		$this->response = $response;
	}

	public static function getInstance($args) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self($args['request'], $args['response']);
		}

		return $instance;
	}

	public function restGet() {

		$name = strtolower($this->request->getParam('name'));

		if (empty ($name)) {
			throw new Aitsu_Api_ParameterMissing_Exception('The name of the view must be specified.');
		}

		if (file_exists(realpath(APPLICATION_PATH . '/../library/Aitsu/Api/Ressource/View/Queries/' . $name))) {
			$query = file_get_contents(realpath(APPLICATION_PATH . '/../library/Aitsu/Api/Ressource/View/Queries/' . $name . '.sql'));
		} else {
			$query = file_get_contents(realpath(APPLICATION_PATH . '/application/queries/' . $name . '.sql'));
		}

		$params = array();
		if (preg_match_all('/\\:(\\w*)/', $query, $matches) > 0) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				$param = $this->request->getParam($matches[1][$i]);
				if (empty ($param)) {
					throw new Aitsu_Api_ParameterMissing_Exception('The parameter ' . $matches[1][$i] . ' is used in the query and therefore needs to be specified.');
				}
				$params[$matches[0][$i]] = $param;
			}
		}
		
		$this->response->body = Aitsu_Db :: fetchAll($query, $params);
	}

	public function restPut() {

		throw new Aitsu_Api_MethodNotSupported_Exception('The put method is not supported.');
	}

	public function restPost() {

		throw new Aitsu_Api_MethodNotSupported_Exception('The post method is not supported.');
	}

	public function restDelete() {

		throw new Aitsu_Api_MethodNotSupported_Exception('The delete method is not supported.');
	}

	public function restHeader() {
		
		throw new Aitsu_Api_MethodNotSupported_Exception('The header method is not supported.');
	}

}
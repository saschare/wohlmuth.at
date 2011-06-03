<?php


/**
 * Article ressource.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Article.php 18037 2010-08-10 11:06:32Z akm $}
 */

class Aitsu_Api_Ressource_Article implements Aitsu_Api_Rest_Interface {

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

		$idartlang = $this->request->getParam('idartlang');

		if (empty ($idartlang)) {
			throw new Aitsu_Api_ParameterMissing_Exception('Parameter missing. Either idartlang or a combination of idart and idlang or idcat and idlang is expected.');
		}

		$this->response->body = Aitsu_Core_Article :: factory($this->request->getParam('idartlang'))->content;
	}

	public function restPut() {
		
		$idartlang = $this->request->getParam('idartlang');
		$method = $this->request->getParam('method');
		
		$this->response->body = print_r($this->request->getHeaders(), true);
		
		/*if (empty ($idartlang)) {
			throw new Aitsu_Api_ParameterMissing_Exception('Please specify an idartlang.');
		}

		if (empty ($method)) {
			throw new Aitsu_Api_ParameterMissing_Exception('Please specify a method.');
		}*/
	}

	public function restPost() {
	}

	public function restDelete() {
	}

	public function restHeader() {
	}

}
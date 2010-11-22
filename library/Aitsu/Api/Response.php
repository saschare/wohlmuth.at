<?php


/**
 * API response object.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Response.php 17669 2010-07-21 14:42:40Z akm $}
 */

class Aitsu_Api_Response {

	const STATUS_OK = 1;
	const STATUS_FAILURE = 0;

	public $api;
	public $status;
	public $body = '';

	protected function __construct($api) {

		$this->status = (object) array (
			'status' => self :: STATUS_OK,
			'message' => ''
		);
		$this->api = $api;
	}

	public static function factory($api) {

		return new self($api);
	}

	public function setStatus($status, $message = null) {

		$this->status = (object) array (
			'status' => $status,
			'message' => $message
		);

		return $this;
	}

	public function __toString() {

		if (empty ($this->body)) {
			return '';
		}

		if (is_object($this->body) || is_array($this->body)) {
			return var_export($this->body, true);
		}

		return $this->body;
	}

	public function get($format = null) {

		$format = '_format' . ucfirst(empty ($format) ? $this->api->format : $format);

		return $this-> $format ();
	}

	protected function _formatXml() {

		return Aitsu_Serializer_Xml :: get($this, 'response');
	}

	protected function _formatJson() {

		return json_encode($this);
	}

	protected function _formatHtml() {

		return (string) $this;
	}

	protected function _formatPlain() {

		return (string) $this;
	}

	public function sendHeader() {

		if ($this->api->format == 'json') {
			header('Content-Type: application/json');
		}

		if ($this->api->format == 'xml') {
			header('Content-Type: text/xml');
		}

		if ($this->api->format == 'html') {
			header('Content-Type: text/html');
		}

		if ($this->api->format == 'plain') {
			header('Content-Type: text/plain');
		}
	}
}
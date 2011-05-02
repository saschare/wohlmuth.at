<?php


/**
 * Email form processor.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Email.php 15763 2010-04-01 14:08:52Z akm $}
 */

class Aitsu_Form_Processor_Email {

	protected $args = array ();

	protected function __construct($redirect, $args) {

		$this->args = $args;
		$this->redirect = $redirect;
	}

	public static function factory($redirect, $args = null) {

		return new self($redirect, $args);
	}

	protected function _send() {
		
		$mail = new Zend_Mail('UTF-8');
		$mail->setFrom($this->args['sendermail'], $this->args['sendername']);
		$mail->addTo($this->args['recepientmail'], $this->args['recepientname']);
		$mail->setSubject($this->args['subject']);
		
		if (isset($_FILES)) {
			foreach ($_FILES as $file) {
				if ($file['size'] > 0) {
					$at = $mail->createAttachment(file_get_contents($file['tmp_name']));
					$at->type = $file['type'];
					$at->filename = $file['name'];				
				}
			}
		}

		$mail->setBodyText($this->_getMessage());

		$mail->send();
	}
	
	protected function _getMessage() {
		
		$message = '';
		$exclusions = isset($this->args['exclude']) ? $this->args['exclude'] : array();
		
		foreach ($_POST as $key => $value) {
			if (substr($key, 0, 1) != '_' && !in_array($key, $exclusions)) {
				if (strlen($message) > 0) {
					$message .= "\n\n";
				}
				$message .= $key . ' : ';
				if (is_array($value)) {
					$first = true;
					foreach ($value as $pos) {
						if ($first) {
							$first = false;
							$message .= str_repeat(' ', 25 - strlen($key . ' : '));
							$message .= $pos;
						} else {
							$message .= "\n";
							$message .= str_repeat(' ', 25);
							$message .= $pos;
						}
					}
				} else {
					if (strlen($value) > 25) {
						$message .= "\n    " . $value;
					} else {
						$message .= str_repeat(' ', 25 - strlen($key . ' : '));
						$message .= wordwrap($value, 80);
					}					
				}
			}
		}
	
		return $message;
	}
	
	public function process() {
		
		$this->_send();
		
		ob_end_clean();
		header('Location: ' . $this->redirect);
		exit(0);
	}
}
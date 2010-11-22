<?php


/**
 * Standard contact form
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17227 2010-06-21 09:22:09Z akm $}
 */

class Aitsu_Ee_Module_ContactForm_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Contact form',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Configurable contact form'),
			'type' => 'Interaction',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725363-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$index = $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$redirectTo = Aitsu_Ee_Config_Text :: set($index, 'redirectTo', '', 'Redirect');
		$senderMail = Aitsu_Ee_Config_Text :: set($index, 'senderMail', 'Email', 'Sender');
		$senderName = Aitsu_Ee_Config_Text :: set($index, 'senderName', 'Name', 'Sender');
		$receipientMail = Aitsu_Ee_Config_Text :: set($index, 'receipientMail', 'Email', 'Receipient');
		$receipientName = Aitsu_Ee_Config_Text :: set($index, 'receipientName', 'Name', 'Receipient');
		$subject = Aitsu_Ee_Config_Text :: set($index, 'subject', '', 'Subject');

		$fields = (array) $params->field;

		$cf = Aitsu_Form_Validation :: factory('contactForm');

		foreach ($fields as $name => $attr) {
			$validation = 'NoTags';
			if (isset ($attr->validation)) {
				$validation = $attr->validation;
			}
			$maxlength = isset ($attr->maxlength) ? $attr->maxlength : 255;
			if ($name == 'message') {
				$maxlength = 4000;
			}
			$cf->setValidator($name, $validation, array (
				'maxlength' => $maxlength
			), isset ($attr->required) && $attr->required == 1);
		}

		$cf->process(Aitsu_Form_Processor_Email :: factory($redirectTo, array (
			'sendermail' => $senderMail,
			'sendername' => $senderName,
			'recepientmail' => $receipientMail,
			'recepientname' => $receipientName,
			'subject' => $subject
		)));

		$code = '';
		if (Aitsu_Registry :: isEdit()) {
			$parameters = str_replace("\r\n", "\n", $context['params']);
			$code = '<code class="aitsu_params" style="display:none;">' . $parameters . '</code>';
		}

		$instance = new self();
		$view = $instance->_getView();

		$view->action = Aitsu_Util :: getCurrentUrl();
		$view->field = $params->field;

		return $code . $view->render($params->template . '.phtml');
	}
}
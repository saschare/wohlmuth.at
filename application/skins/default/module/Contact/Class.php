<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_Contact_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Contact',
			'description' => Aitsu_Translate :: translate('Returns a simple contact form'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.1',
			'status' => 'stable',
			'url' => null,
			'id' => '4de4d759-5b88-49bb-aed4-604f50431bca'
		);
	}
	
	public static function init($context) {

		$index = $context['index'];
		
		$instance = new self();

		$redirectTo = Aitsu_Content_Config_Text :: set($index, 'ContactRedirectTo', 'Redirect', 'Redirect');
		$senderMail = Aitsu_Content_Config_Text :: set($index, 'ContactSenderMail', 'E-Mail', 'Sender');
		$senderName = Aitsu_Content_Config_Text :: set($index, 'ContactSenderName', 'Name', 'Sender');
		$receipientMail = Aitsu_Content_Config_Text :: set($index, 'ContactReceipientMail', 'E-Mail', 'Receipient');
		$receipientName = Aitsu_Content_Config_Text :: set($index, 'ContactReceipientName', 'Name', 'Receipient');
		$subject = Aitsu_Content_Config_Text :: set($index, 'ContactSubject', 'Subject', 'Form');

		$redirectTo = $redirectTo ? $redirectTo : '/';
		$senderMail = $senderMail ? $senderMail : 'mail@domain.tld';
		$senderName = $senderName ? $senderName : 'webserver mail';
		$receipientMail = $receipientMail ? $receipientMail : 'john@do.com';
		$receipientName = $receipientName ? $receipientName : 'John Do';
		$subject = $subject ? $subject : Aitsu_Translate :: _('Direct contact');

		$view = $instance->_getView();

		$view->subject = $subject;

		$cf = Aitsu_Form_Validation :: factory('contactForm');

			$cf->setValidator('name', 'NoTags', array('maxlength' => 100), true);
			$cf->setValidator('company', 'NoTags', array('maxlength' => 100), false);
			$cf->setValidator('telephone', 'NoTags', array('maxlength' => 30), false);
			$cf->setValidator('email', 'Email', null, true);
			$cf->setValidator('message', 'NoTags', array('maxlength' => 5000), true);

		$cf->process(Aitsu_Form_Processor_Email :: factory($redirectTo, array (
			'sendermail' => $senderMail,
			'sendername' => $senderName,
			'recepientmail' => $receipientMail,
			'recepientname' => $receipientName,
			'subject' => $subject . ' :: ' . Aitsu_Util :: getCurrentUrl()
		)));

		$output = $view->render('index.phtml');

		return $output;
	}
}
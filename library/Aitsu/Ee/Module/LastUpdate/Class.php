<?php


/**
 * Last updated.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17323 2010-06-25 15:26:49Z akm $}
 */

class Aitsu_Ee_Module_LastUpdate_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Last update',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns the timestamp of the last modification of the current article.'),
			'type' => array (
				'Header',
				'Content'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a072536e-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$index = $context['index'];
		Aitsu_Content_Edit :: noEdit('LastUpdate', true);

		$idartlang = Aitsu_Registry :: get()->env->idartlang;

		$date = Aitsu_Db :: fetchOne('' .
		'select modified from _article_content ' .
		'where idartlang = ? ' .
		'order by modified desc ' .
		'limit 0, 1 ', array (
			$idartlang
		));

		/*
		 * Der Gebrauch der Zend-Lokalisierung erzeugt einen erheblichen Überhang, der vermieden
		 * werden sollte.
		 */
		// return sprintf(Aitsu_Translate :: _('Last update on %s'), Aitsu_Util_Date :: long($date));

		return sprintf(Aitsu_Translate :: _('Last update on %s'), $date);
	}
}
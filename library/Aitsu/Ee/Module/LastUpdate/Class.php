<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_LastUpdate_Class extends Aitsu_Ee_Module_Abstract {

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
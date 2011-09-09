<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Init_Auto_LanguagePreselector implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (!Aitsu_Config :: get('rewrite.uselang')) {
			/*
			 * Use language not set in rewriting.
			 */
			return;
		}
		
		if (!isset($_GET['url'])) {
			/*
			 * URL parameter not set.
			 */
			return;
		}
		
		if (strlen($_GET['url']) > strlen(Aitsu_Config :: get('sys.mainDir')) - 1) {
			/*
			 * We are not on top level.
			 */
			return;
		}

		if (empty ($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			/*
			 * No accept language header set.
			 */
			return;
		}

		if (preg_match_all('/([^;]*)\\;q\\=([0-9\\.]*)/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches) == 0) {
			/*
			 * Format of accept language header is not in the expected format.
			 */
			return;
		}

		$langs = array ();
		for ($i = 0; $i < count($matches[0]); $i++) {
			$langs[$matches[1][$i]] = $matches[2][$i];
		}
		asort($langs, SORT_NUMERIC);
		$langs = array_keys($langs);
		array_walk($langs, array (
			'Aitsu_Init_Auto_LanguagePreselector',
			'evalAcceptLang'
		));

		foreach ($langs as $lang) {
			if ($lang != null) {
				header('Location: ' . Aitsu_Config :: get('sys.mainDir') . $lang . '/');
				exit(0);
			}
		}
	}

	protected static function evalAcceptLang(& $item, $key) {

		static $availableLangs;

		if (!isset ($availableLangs)) {
			$availableLangs = array();
			$langs = Aitsu_Db :: fetchAll('' .
			'select ' .
			'	name, ' .
			'	locale ' .
			'from _lang ' .
			'where ' .
			'	idclient = :idclient', array (
				':idclient' => Aitsu_Config :: get('sys.client')
			));
			foreach ($langs as $lang) {
				$availableLangs[$lang['locale']] = $lang['name'];
				$availableLangs[substr($lang['locale'], 0, 2)] = $lang['name'];
			}
		}
		
		foreach (explode(',', $item) as $locale) {
			if (isset($availableLangs[$locale])) {
				$item = $availableLangs[$locale];
				return;
			}
		}
		
		$item = null;
	}
}
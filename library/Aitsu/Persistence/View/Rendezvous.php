<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Persistence_View_Rendezvous {

	protected function __construct() {
	}

	protected static function _instance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function getDates(Aitsu_Util_Date $from, Aitsu_Util_Date $to, $category = null) {

		$returnValue = array ();

		if ($category == null) {
			$cat['lft'] = 0;
			$cat['rgt'] = PHP_INT_MAX;
		} else {
			$cat = Aitsu_Db :: fetchRow('' .
			'select lft, rgt from _cat where idcat = :idcat', array (
				':idcat' => $category
			));
		}

		$client = Aitsu_Registry :: get()->env->idclient;

		$dates = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	rv.*, ' .
		'	artlang.idart, ' .
		'	artlang.pagetitle, ' .
		'	artlang.teasertitle, ' .
		'	artlang.summary, ' .
		'	cat.idcat, ' .
		'	catlang.name catname ' .
		'from _rendezvous rv ' .
		'left join _art_lang artlang on rv.idart = artlang.idart ' .
		'left join _cat_art catart on artlang.idart = catart.idart ' .
		'left join _cat cat on catart.idcat = cat.idcat ' .
		'left join _cat_lang catlang on cat.idcat = catlang.idcat and catlang.idlang = artlang.idlang ' .
		'where ' .
		'	artlang.idlang = :idlang ' .
		'	and artlang.online = 1 ' .
		'	and cat.lft between :lft and :rgt ' .
		'	and (' .
		'		(rv.periodicity = 0 and rv.starttime > :from and rv.starttime < :to) ' .
		'		or ' .
		'		(rv.periodicity > 0 and rv.starttime < :to and (rv.until is null or rv.until > :to)) ' .
		'	) ', array (
			':idlang' => Aitsu_Registry :: get()->env->idlang,
			':lft' => $cat['lft'],
			':rgt' => $cat['rgt'],
			':from' => $from->get('Y-m-d H:i:s'),
			':to' => $to->get('Y-m-d H:i:s')
		));

		for ($day = $from->getTime(); $day <= $to->getTime() - 1; $day = $day +24 * 60 * 60) {
			foreach ($dates as $date) {
				$startTime = Aitsu_Util_Date :: fromMySQL($date['starttime'])->getStartOfDay();
				$until = $date['until'] == null ? PHP_INT_MAX : Aitsu_Util_Date :: fromMySQL($date['until'])->getTime();
				if ($day == $startTime && $date['periodicity'] == 0) {
					$date['starttime'] = Aitsu_Util_Date :: fromMySQL($date['starttime']);
					$date['endtime'] = Aitsu_Util_Date :: fromMySQL($date['endtime']);
					$returnValue[$date['starttime']->getTime() . uniqid()] = (object) $date;
				}
				elseif ($date['periodicity'] > 0 && $startTime < $day && $until > $day && (($day - $startTime) / 60 / 60 / 24) % $date['periodicity'] == 0) {
					$date['starttime'] = Aitsu_Util_Date :: fromMySQL($date['starttime'])->add($day - $startTime);
					$date['endtime'] = Aitsu_Util_Date :: fromMySQL($date['endtime'])->add($day - $startTime);
					$returnValue[$date['starttime']->getTime() . uniqid()] = (object) $date;
				}
			}
		}

		ksort($returnValue);

		return $returnValue;
	}
}
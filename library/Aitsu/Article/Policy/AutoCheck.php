<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_AutoCheck implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {

		$idartlang = $event->idartlang;

		$policies = Aitsu_Config :: get('policy.article');

		if (empty ($policies)) {
			return;
		}

		$activePolicies = array ();

		foreach (Aitsu_Config :: get('policy.article') as $policy) {
			if (self :: _conditionMet($idartlang, $policy) && isset ($policy->enable) && $policy->enable) {
				/*
				 * Check and register policy.
				 */
				$statement = isset ($policy->statement) ? $policy->statement : null;
				$ap = Aitsu_Article_Policy_Factory :: get($policy->policy, $statement, $idartlang);
				$ap->isFullfilled();
				$activePolicies[] = $ap->getPolicyArtId();
			}
		}

		if (empty ($activePolicies)) {
			Aitsu_Db :: query('' .
			'delete from _policy_art ' .
			'where ' .
			'	idartlang = :idartlang', array (
				':idartlang' => $idartlang
			));
		} else {
			Aitsu_Db :: query('' .
			'delete from _policy_art ' .
			'where ' .
			'	idartlang = :idartlang ' .
			'	and policyartid not in (' . implode(',', $activePolicies) . ')', array (
				':idartlang' => $idartlang
			));
		}
	}

	protected static function _conditionMet($idartlang, $policy) {

		if (!isset ($policy->condition)) {
			return true;
		}

		$conditions = $policy->condition->toArray();
		$bind = array (
			':idartlang' => $idartlang
		);

		foreach ($conditions as $key => $condition) {
			if (strtolower($key) == 'lang') {
				$lang = Aitsu_Db :: fetchOne('' .
				'select l.name ' .
				'from _art_lang a ' .
				'left join _lang l on a.idlang = l.idlang ' .
				'where ' .
				'	a.idartlang = :idartlang ' .
				'limit 0, 1', $bind);
				if (!$lang || $lang != $condition) {
					return false;
				}
			}
			elseif (strtolower($key) == 'incat') {
				$idcat = Aitsu_Db :: fetchOne('' .
				'select c.idcat ' .
				'from _art_lang a ' .
				'left join _cat_art c on a.idart = c.idart ' .
				'where ' .
				'	a.idartlang = :idartlang ' .
				'limit 0, 1', $bind);
				if (!$idcat || $idcat != $condition) {
					return false;
				}
			}
			elseif (strtolower($key) == 'belowcat') {
				if (Aitsu_Db :: fetchOne('' .
					'select count(child.idcat) ' .
					'from ' .
					'	_art_lang artlang, ' .
					'	_cat_art catart, ' .
					'	_cat parent, ' .
					'	_cat child ' .
					'where ' .
					'	artlang.idart = catart.idart ' .
					'	and catart.idcat = child.idcat ' .
					'	and child.lft between parent.lft and parent.rgt ' .
					'	and artlang.idartlang = :idartlang ' .
					'	and parent.idcat = :idcat', array_merge($bind, array (
						':idcat' => $condition
					))) == 0) {
					return false;
				}
			}
		}

		return true;
	}

}
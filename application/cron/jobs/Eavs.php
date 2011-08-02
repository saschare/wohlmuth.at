<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class CronJob_Eavs extends Aitsu_Cron_Job_Abstract {

	protected function _isPending($lasttime) {

		return true;
	}

	protected function _exec() {

		Aitsu_Db :: query('delete from _eavs_e');

		for ($e = 1; $e < 250000; $e++) {

			/*
			 * Entitites.
			 */
			Aitsu_Db :: query('' .
			'insert into _eavs_e (eid, identifier) values (:eid, :identifier)', array (
				':eid' => $e,
				':identifier' => 'e' . str_pad($e, 6, '0', STR_PAD_LEFT)
			));

			/*
			 * Values.
			 */
			for ($s = 1; $s <= 5; $s++) {
				for ($a = 1; $a <= 9; $a++) {
					Aitsu_Db :: query('' .
					'insert into _eavs_v (ent, att, src, charval) values (:ent, :att, :src, :charval)', array (
						':ent' => $e,
						':att' => $a,
						':src' => $s,
						':charval' => "s$s e$e a$a"
					));
				}
			}

			if ($e % 10 == 0) {
				Aitsu_Db :: query('call updateEavs');
				echo $e . ' records inserted.' . "\n";
			}
		}
		
		Aitsu_Db :: query('call updateEavs');
	}

}
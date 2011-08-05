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
		
		$blockStart = null;

		$eid = Aitsu_Db :: fetchOne('select max(eid) from _eavs_e');
		Aitsu_Db :: query('delete from _eavs_e where eid = :eid', array (
			':eid' => $eid
		));

		for ($e = $eid; $e < 250000; $e++) {

			$startTime = microtime(true);

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
				if (rand(0, 1000) % 2 == 0) {
					for ($a = 1; $a <= 9; $a++) {
						if (rand(0, 1000) % 2 == 0) {
							Aitsu_Db :: query('' .
							'insert into _eavs_v (ent, att, src, charval) values (:ent, :att, :src, :charval)', array (
								':ent' => $e,
								':att' => $a,
								':src' => $s,
								':charval' => "s$s e$e a$a"
							));
						}
					}
				}
			}

			$execTime = (microtime(true) - $startTime) * 1000;

			// echo str_pad($e, 6, '0', STR_PAD_LEFT) . ': Insert time for one entity: ' . number_format($execTime, 1) . ' ms' . "\n";
			echo '.';

			if ($e % 500 == 0) {
				$startTime = microtime(true);
				Aitsu_Db :: query('call updateEavs');
				$execTime = (microtime(true) - $startTime) * 1000;
				echo "\n" . 'Call of updateEavs done: ' . number_format($execTime, 1) . ' ms' . "\n";
				
				$blockEnd = microtime(true);
				
				if ($blockStart != null) {
					$period = ($blockEnd - $blockStart);
					echo 'Last block (500 items) needed: ' . number_format($period, 1) . ' s' . "\n";
					$estimatedTime = (250000 - $e) / 500 * $period / 60 / 60;
					echo 'Estimated time to finish: ' . number_format($estimatedTime, 2) . ' hours' . "\n";
				}
				
				$blockStart = $blockEnd;
			}
		}

		Aitsu_Db :: query('call updateEavs');
	}

}
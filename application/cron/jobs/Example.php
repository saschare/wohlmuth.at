<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class CronJob_Example extends Aitsu_Cron_Job_Abstract {

	protected function _isPending($lasttime) {

		/*
		 * Weekly execution on saturday.
		 */

		if ($lasttime +1 * 24 * 60 * 60 > time()) {
			/*
			 * Last execution has been made less than one ago.
			 */
			return false;
		}

		$weekday = date('w');
		if (!in_array($weekday, array (
				6
			))) {
			/*
			 * Current day is not saturday.
			 */
			return false;
		}

		return true;
	}

	protected function _exec() {

		/*
		 * Do here what has to be done.
		 */
	}

}
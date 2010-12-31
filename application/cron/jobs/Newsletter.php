<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class CronJob_Newsletter extends Aitsu_Cron_Job_Abstract {
	
	protected function _isPending($lasttime) {
		
		/*
		 * Weekly execution on saturday or sunday.
		 */
		 	
		// if ($lasttime + 6.5 * 24 * 60 * 60 > time()) {
		if ($lasttime + 5 > time()) {
			/*
			 * Last execution has been made less than 6.5 d ago.
			 */			 	 
			return false;
		}
		
		$weekday = date('w');
		if (!in_array($weekday, array(0, 6, 5))) {
			/*
			 * Current day is neither saturday nor sunday.
			 */
			return false;
		}
		
		return true;
	}

	protected function _exec() {
		
		echo 'execution made';
	}
	
}
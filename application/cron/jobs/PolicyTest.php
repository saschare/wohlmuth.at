<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class CronJob_PolicyTest extends Aitsu_Cron_Job_Abstract {

	protected function _isPending($lasttime) {

		return true;
	}

	protected function _exec() {

		echo var_export(Aitsu_Article_Policy_Factory :: get('ExistsInLanguage', 'de', 534)->isFullfilled(), true);
	}

}
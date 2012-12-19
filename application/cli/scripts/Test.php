<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Cli_Test extends Aitsu_Cli_Script_Abstract {

	protected function _main() {

		$options = getopt('u::p::s:t:');

		call_user_func(array (
			$options['t'],
			'run'
		));
	}
}
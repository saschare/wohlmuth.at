<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Cli_Test extends Aitsu_Cli_Script_Abstract {

	protected function _main() {
		
		Aitsu_Registry :: get()->env->idclient = 1;

		echo var_export(Aitsu_Article_Policy_Factory :: get('ExistsInLanguage', 'de fr', 532)->isFullfilled(), true);
		echo "\n";
	}
}
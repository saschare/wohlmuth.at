<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Cli_Installation extends Aitsu_Cli_Script_Abstract {

	protected function _main() {

		include_once APPLICATION_PATH . '/adm/scripts/Installation/Setup.php';

		$response = 'START';
		$counter = 1;
		while ($response != null) {
			$installScript = new Adm_Script_Setup($counter);
			$response = $installScript->exec()->toArray();
			echo $counter . ': ' . $response['message'] . "\n";
			if (empty($response['nextStep'])) {
				$response = null;
			}
			if ($response['nextStep'] != 'RESUME') {
				$counter++;
			}
		}
		
		echo "\n" . 'Script execution terminated.' . "\n";
	}
}
<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Cli_Update extends Aitsu_Cli_Script_Abstract {

    protected function _main() {

        include_once APPLICATION_PATH . '/adm/scripts/Update/Synchronize_Database_Structure.php';

        $response = 'START';
        $counter = 1;
        while ($response != null) {
            $updateScript = new Adm_Script_Synchronize_Database_Structure($counter);
            $response = $updateScript->exec()->toArray();
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
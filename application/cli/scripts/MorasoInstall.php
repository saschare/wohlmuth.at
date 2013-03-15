<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Cli_MorasoInstall extends Aitsu_Cli_Script_Abstract {

    protected function _main() {

        include_once APPLICATION_PATH . '/adm/scripts/Installation/Setup.php';
        include_once APPLICATION_PATH . '/adm/scripts/Update/Synchronize_Database_Structure.php';
        include_once APPLICATION_PATH . '/adm/scripts/Update/Create_Basic_Structure.php';

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

            if ($response != null) {
                $updateScript = new Adm_Script_Synchronize_Database_Structure($counter);
                $response = $updateScript->exec()->toArray();

                echo $counter . ': ' . $response['message'] . "\n";

                if (empty($response['nextStep'])) {
                    $response = null;
                }

                if ($response['nextStep'] != 'RESUME') {
                    $counter++;
                }

                if ($response != null) {
                    $morasoBasicStructureScript = new Adm_Script_Create_Basic_Structure($counter);
                    $response = $updateScript->exec()->toArray();

                    echo $counter . ': ' . $response['message'] . "\n";

                    if (empty($response['nextStep'])) {
                        $response = null;
                    }

                    if ($response['nextStep'] != 'RESUME') {
                        $counter++;
                    }
                }
            }
        }

        echo "\n" . 'Script execution terminated.' . "\n";
    }

}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Adm_Script_Update_2_0_10_3 extends Aitsu_Adm_Script_Abstract {

	protected $_from;
	protected $_to;
	protected $_lines = array ();

	public static function getName() {

		return Aitsu_Translate :: translate('Update from 2.0.10.2');
	}

	protected function _hasNext() {

		$path = APPLICATION_PATH . '/data';
		if (!is_writable($path)) {
			throw new Exception(sprintf(Aitsu_Translate :: translate('Directory with path %s is not writable. Please allow the server to write to this directory and underneath.'), $path));
		}
		$dirs = scandir($path);
		foreach ($dirs as $dir) {
			if ($dir != '.' && $dir != '..') {
				if (!is_writable($path . '/' . $dir)) {
					throw new Exception(sprintf(Aitsu_Translate :: translate('File or directory with path %s is not writable. Please allow the server to write to %s and underneath.'), $path . '/' . $dir, $path));
				}
			}
		}

		$step = $this->_currentStep + 1;

		$handle = fopen(dirname(__FILE__) . '/2.0.10.2-2.0.10.3.sql', 'r');

		if (!$handle) {
			throw new Exception(Aitsu_Translate :: translate('The file 2.0.10.2-2.0.10.3.sql is missing.'));
		}

		$line = 0;
		$this->_from = ($step -1) * 100 + 1;
		$this->_to = $step * 100;
		while (!feof($handle)) {
			$line++;
			$buffer = fgets($handle, 50000);
			if ($line >= $this->_from && $line <= $this->_to) {
				$this->_lines[] = $buffer;
			}
		}
		fclose($handle);
		
		if (count($this->_lines) == 0) {
			Aitsu_Db :: query('set foreign_key_checks = 1', null, true);

			return false;
		}

		return true;
	}

	protected function _next() {

		return sprintf(Aitsu_Translate :: translate('Executing from line %s to %s.'), ($this->_from + 100), ($this->_to + 100));
	}

	protected function _executeStep() {

		Aitsu_Db :: query('set foreign_key_checks = 0', null, true);
		foreach ($this->_lines as $line) {
			if (preg_match_all("/'(.*?)(?<!\\\\)'/", $line, $matches) > 0) {
				$params = array ();
				for ($i = 0; $i < count($matches[0]); $i++) {
					$line = str_replace($matches[0][$i], '?', $line);
					$params[] = stripslashes(str_replace(array (
						'\r',
						'\n'
					), array (
						"\r",
						"\n"
					), $matches[1][$i]));
				}
				Aitsu_Db :: query(str_replace('`ait_', '`' . Aitsu_Registry :: get()->config->database->params->tblprefix, $line), $params, true);
			} else {
				Aitsu_Db :: query(str_replace('`ait_', '`' . Aitsu_Registry :: get()->config->database->params->tblprefix, $line), null, true);
			}
		}

		$response = sprintf(Aitsu_Translate :: translate('Line %s to %s executed.'), $this->_from, $this->_to);
		return Aitsu_Adm_Script_Response :: factory($response);
	}

}
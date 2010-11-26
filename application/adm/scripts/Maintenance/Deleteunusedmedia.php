<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Adm_Script_Deleteunusedmedia extends Aitsu_Adm_Script_Abstract {

	protected $_medium = null;

	public static function getName() {

		return Aitsu_Translate :: translate('Delete unlinked media from the file system');
	}

	protected function _hasNext() {

		$medium = Aitsu_Db :: fetchRow('' .
		'select * from _media where deleted is not null limit 0, 1');

		if (!$medium) {
			return false;
		}

		$this->_medium = (object) $medium;

		return true;
	}

	protected function _next() {

		return sprintf(Aitsu_Translate :: translate('Removing %s (ID %s)'), $this->_medium->idart . '/' . $this->_medium->filename, $this->_medium->mediaid);
	}

	protected function _executeStep() {

		$filename = APPLICATION_PATH . '/data/media/' . $this->_medium->idart . '/' . $this->_medium->mediaid . '.' . $this->_medium->extension;

		if (!is_writable($filename)) {
			throw new Exception('File cannot be deleted: ' . $filename);
		}

		@ unlink($filename);
		@ rmdir(dirname($filename));

		if (!file_exists($filename)) {
			Aitsu_Db :: query('' .
			'delete from _media where mediaid = :id', array (
				':id' => $this->_medium->mediaid
			));
		}

		$response = sprintf(Aitsu_Translate :: translate('File %s removed.'), APPLICATION_PATH . '/data/media/' . $this->_medium->mediaid . '.' . $this->_medium->extension);
		return Aitsu_Adm_Script_Response :: factory($response);
	}

}
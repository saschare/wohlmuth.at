<?php


/**
 * The class extracts the meta information available in PDF files and 
 * populates the data into the file's meta information in all available
 * languages.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_File_AutoFillMetaData implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {

		if (strtolower($event->file->extension) != 'pdf') {
			return;
		}

		try {
			$pdf = Zend_Pdf :: load($event->path);
		} catch (Exception $e) {
			return;
		}

		$event->file->medianame = isset($pdf->properties['Title']) ? $pdf->properties['Title'] : '';
		$event->file->subline = isset($pdf->properties['Subject']) ? $pdf->properties['Subject'] : '';
		$event->file->description = isset($pdf->properties['Subject']) ? $pdf->properties['Subject'] : '';

		$langs = Aitsu_Db :: fetchCol('' .
		'select lang.idlang from _lang lang, _lang current ' .
		'where ' .
		'	lang.idclient = current.idclient ' .
		'	and current.idlang = :idlang', array (
			':idlang' => Aitsu_Registry :: get()->env->idlang
		));

		foreach ($langs as $idlang) {
			$event->file->idlang = $idlang;
			$event->file->save();
		}
	}

}
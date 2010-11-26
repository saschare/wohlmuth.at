<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Index_Mysql implements Aitsu_Event_Listener_Interface {
	
	public static function notify(Aitsu_Event_Abstract $event) {
		
		if ($event->action == 'save') {
			self :: _populateIndexTable($event->idartlang);
		}
		
		if ($event->acton == 'delete') {
			self :: _removeIndexEntry();
		}
	}

	protected static function _populateIndexTable($idartlang) {

		$content = '';

		$contents = Aitsu_Db :: fetchCol('' .
		'select content.value from _article_content as content ' .
		'where idartlang = :idartlang ' .
		'order by content.index asc', array (
			':idartlang' => $idartlang
		));

		if ($contents) {
			foreach ($contents as $fragment) {
				$content .= strip_tags($fragment) . ' ';
			}
		}

		$content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');
		$content = preg_replace('/[\\n\\r\\s]+/', ' ', $content);

		Aitsu_Db :: query('' .
		'insert into _content_index ' .
		'(idartlang, content) values (:idartlang, :content) ' .
		'on duplicate key update content = :content', array (
			':idartlang' => $idartlang,
			':content' => $content
		));
	}

	protected static function _removeIndexEntry() {

		Aitsu_Db :: query('' .
		'delete mindex ' .
		'from _content_index as mindex ' .
		'left join _art_lang as artlang on mindex.idartlang = artlang.idartlang ' .
		'where artlang.idartlang is null');
	}
}
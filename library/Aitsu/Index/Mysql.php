<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Mysql.php 19473 2010-10-21 14:02:09Z akm $}
 */

class Aitsu_Index_Mysql implements Aitsu_Event_Listener_Interface {
	
	public static function notify(Aitsu_Event_Abstract $event) {
		
		static $executed = false;
		
		if ($executed) {
			/*
			 * postDispatch is fired twice. Only in the second case we
			 * see updated data. We therefore do not return at this point.
			 */
			// return;
		}
		
		if ($event->request->getParam('plugin') == 'edit' && $event->request->getParam('paction') == 'save') {
			self :: _populateIndexTable($event->request);
		}
		
		if ($event->request->getParam('controller') == 'data' && $event->request->getParam('action') == 'delete') {
			self :: _removeIndexEntry($event->request);
		}
		
		$executed = true;
	}

	protected static function _populateIndexTable($request) {

		$content = '';

		$contents = Aitsu_Db :: fetchCol('' .
		'select content.value from _article_content as content ' .
		'where idartlang = :idartlang ' .
		'order by content.index asc', array (
			':idartlang' => $request->getParam('idartlang')
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
			':idartlang' => $request->getParam('idartlang'),
			':content' => $content
		));
	}

	protected static function _removeIndexEntry($request) {

		trigger_error('löschung wird vorgenommen');

		Aitsu_Db :: query('' .
		'delete mindex ' .
		'from _content_index as mindex ' .
		'left join _art_lang as artlang on mindex.idartlang = artlang.idartlang ' .
		'where artlang.idartlang is null');
	}
}
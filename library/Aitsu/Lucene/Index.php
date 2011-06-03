<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Lucene_Index implements Aitsu_Event_Listener_Interface {

	protected $indexName;
	protected $uid;
	protected $fields;
	protected $document;

	protected static $doIndex = true;
	
	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (!isset($event->bootstrap->pageContent)) {
			return;
		}
		
		Aitsu_Lucene_Index :: indexArticle($event->bootstrap->pageContent);
	}

	protected function __construct() {
	}

	protected static function _init() {

		Zend_Search_Lucene_Analysis_Analyzer :: setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
	}

	public static function update($indexName, $uid, $fields, $document) {

		static $instance;

		if (!isset ($instance[$indexName])) {
			$instance[$indexName] = new self();
			self :: _init();
		}

		$instance[$indexName]->indexName = $indexName;
		$instance[$indexName]->uid = $uid;
		$instance[$indexName]->fields = $fields;
		$instance[$indexName]->document = $document;
		$instance[$indexName]->path = APPLICATION_PATH . '/data/lucene';

		$instance[$indexName]->_index();
	}

	protected function _index() {

		if (!file_exists($this->path)) {
			mkdir($this->path, 0777, true);
		}

		$indexPath = $this->path . '/' . $this->indexName;
		if (!is_dir($indexPath)) {
			$index = Zend_Search_Lucene :: create($indexPath);
		} else {
			$index = Zend_Search_Lucene :: open($indexPath);
		}

		$hits = $index->find('uid:' . $this->uid);
		foreach ($hits as $hit) {
			$index->delete($hit->id);
		}

		if (is_a($this->document, 'Zend_Search_Lucene_Document')) {
			$doc = $this->document;
		} else {
			$doc = new Zend_Search_Lucene_Document();
			$doc->addField(Zend_Search_Lucene_Field :: UnStored('content', $this->document, 'UTF-8'));
		}

		$doc->addField(Zend_Search_Lucene_Field :: Text('uid', $this->uid, 'UTF-8'));

		foreach ($this->fields as $key => $value) {
			$doc->addField(Zend_Search_Lucene_Field :: Text($key, $value, 'UTF-8'));
		}

		$index->addDocument($doc);
	}

	public static function indexArticle($html) {

		if (!isset (Aitsu_Registry :: get()->config->search->lucene)) {
			/*
			 * Lucene is not configured.
			 */
			return;
		}

		if (!self :: $doIndex) {
			/*
			 * The index has been used within the current request.
			 * We therefore do not add the result to the index.
			 */
			return;
		}

		$idlang = Aitsu_Registry :: get()->env->idlang;
		$idart = Aitsu_Registry :: get()->env->idart;
		$interval = Aitsu_Registry :: get()->config->search->lucene->refreshRate;

		if (Aitsu_Db :: fetchOne('' .
			'select count(*) ' .
			'from _art_lang as artlang ' .
			'left join _lucene_index as lucene on lucene.idart = artlang.idart and lucene.idlang = artlang.idlang ' .
			'left join _art_meta as metatag on artlang.idartlang = metatag.idartlang ' .
			'where ' .
			'	artlang.idart = :idart ' .
			'	and artlang.idlang = :idlang ' .
			'	and ( ' .
			'		date_add(lucene.lastindexed, interval ' . $interval . ') < now() ' .
			'		or lucene.lastindexed is null ' .
			'		or artlang.lastmodified > lucene.lastindexed ' .
			'		) ' .
			'	and artlang.online = 1 ' .
			'	and ( ' .
			'		metatag.robots not like :noindex ' .
			'		or metatag.idartlang is null ' .
			'		) ', array (
				':idart' => $idart,
				':idlang' => $idlang,
				':noindex' => '%noindex%'
			)) == 0) {
			/*
			 * Index is not outdated yet or article is offline.
			 */
			return;
		}

		$uid = Aitsu_Registry :: get()->env->idart . '-' . Aitsu_Registry :: get()->env->idlang;

		$result = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	idartlang, ' .
		'	pagetitle, ' .
		'	summary ' .
		'from _art_lang ' .
		'where ' .
		'	idart = ? ' .
		'	and idlang = ? ', array (
			$idart,
			$idlang
		));

		$fields = array ();
		$fields['pagetitle'] = $result['pagetitle'];
		$fields['summary'] = $result['summary'];
		$fields['lang'] = $idlang;
		$fields['idart'] = $idart;

		self :: update(Aitsu_Registry :: get()->config->search->lucene->index, $uid, $fields, Zend_Search_Lucene_Document_Html :: loadHTML($html));

		Aitsu_Db :: query('' .
		'replace into _lucene_index ' .
		'(uid, idart, idlang, lastindexed) ' .
		'values ' .
		'(?, ?, ?, now()) ', array (
			$uid,
			$idart,
			$idlang
		));
	}

	public static function find($query, $inCats, $indexName = null) {

		self :: $doIndex = false;

		$searchResults = array ();

		if ($query == null || strlen(trim($query)) == 0) {
			return $searchResults;
		}

		$query .= ' AND lang:' . Aitsu_Registry :: get()->env->idlang;

		self :: _init();

		$indexName = $indexName != null ? $indexName : Aitsu_Registry :: get()->config->search->lucene->index;
		$indexPath = APPLICATION_PATH . '/data/lucene' . '/' . $indexName;
		$index = Zend_Search_Lucene :: open($indexPath);
		Zend_Search_Lucene_Search_QueryParser :: setDefaultEncoding('UTF-8');

		$hits = $index->find($query);

		$inUid = array ();
		foreach ($hits as $hit) {
			if (count($inUid) < 200) {
				$inUid[] = $hit->uid;
			}
		}
		$inUid = "'" . implode("','", $inUid) . "'";

		$inCats = implode(',', $inCats);

		$results = Aitsu_Db :: fetchAll("" .
		"select artlang.idart, artlang.idlang " .
		"from _lucene_index as lucene " .
		"left join _art_lang as artlang on lucene.idart = artlang.idart and lucene.idlang = artlang.idlang " .
		"left join _cat_art as catart on artlang.idart = catart.idart " .
		"left join _cat_lang as catlang on catart.idcat = catlang.idcat and catlang.idlang = artlang.idlang " .
		"left join _cat as node on node.idcat = catart.idcat " .
		"left join _cat as parent on node.lft between parent.lft and parent.rgt " .
		"where " .
		"	lucene.uid in ({$inUid}) " .
		"	and parent.idcat in ({$inCats}) " .
		"	and catlang.public = 1 " .
		"	and catlang.visible = 1 " .
		"	and artlang.online = 1 " .
		"	and artlang.idlang = :idlang ", array (
			':idlang' => Aitsu_Registry :: get()->env->idlang
		));

		if (!$results) {
			return $searchResults;
		}

		$matches = array ();
		foreach ($results as $result) {
			$matches[] = $result['idart'] . '-' . $result['idlang'];
		}

		foreach ($hits as $hit) {
			if (in_array($hit->uid, $matches)) {
				$searchResults[] = $hit;
			}
		}

		return $searchResults;
	}

}
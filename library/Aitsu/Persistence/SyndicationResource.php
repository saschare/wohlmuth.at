<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * The behaviour of this class differs from other persistence classes. The factory expects 
 * to get an array of which the first entry is the syndication source id and the second is 
 * the source idartlang.
 * 
 * The load method on the other hand expects an integer value specifying the maximum age
 * the resource may have to evaluate as a valid syndication resource. If the current value
 * is older, the load methods loads the data from the specified source, updates the database
 * and then populates the object.
 */

class Aitsu_Persistence_SyndicationResource extends Aitsu_Persistence_Abstract {

	protected $_sourceid = null;
	protected $_sourceidartlang = null;
	protected $_data = null;
	protected $_resourceName = null;

	protected function __construct($id) {

		$this->_sourceid = $id[0];
		$this->_sourceidartlang = $id[1];
	}

	public static function factory($id = null) {

		static $instance = array ();

		$identification = $id[0] . '-' . $id[1];

		if (!isset ($instance[$identification])) {
			$instance[$identification] = new self($id);
		}

		return $instance[$identification];
	}

	public function load($maxage = 86400) {

		$resource = Aitsu_Db :: fetchOne('' .
		'select content from _syndication_resource ' .
		'where ' .
		'	sourceid = :sourceid ' .
		'	and sourceidartlang = :sourceidartlang ' .
		'	and loaded > date_sub(now(), interval ' . $maxage . ' second) ', array (
			':sourceid' => $this->_sourceid,
			':sourceidartlang' => $this->_sourceidartlang
		));

		if ($resource) {
			$this->_data = unserialize($resource);
			return $this;
		}

		$source = Aitsu_Db :: fetchRow('' .
		'select * from _syndication_source ' .
		'where sourceid = :sourceid', array (
			':sourceid' => $this->_sourceid
		));

		try {
			$response = Aitsu_Http_Hmac_Sha1 :: factory($source['url'] . 'id/' . $this->_sourceidartlang . '/', $source['userid'], $source['secret'])->addParam('structured', 1)->getResponse();
			$this->_data = Aitsu_Transformation_StructuredTo :: xml($response, true);
			Aitsu_Db :: query('' .
			'insert into _syndication_resource ' .
			'(sourceid, sourceidartlang, content, name) ' .
			'values ' .
			'(:sourceid, :sourceidartlang, :content, :name) ' .
			'on duplicate key update content = :content', array (
				':sourceid' => $this->_sourceid,
				':sourceidartlang' => $this->_sourceidartlang,
				':content' => serialize($this->_data),
				':name' => $this->_resourceName
			));
		} catch (Exception $e) {
			trigger_error($e->getMessage());
		}

		return $this;
	}

	public function __get($key) {

		if ($key == 'data')
			return $this->_data;

		return null;
	}

	public function __set($key, $value) {

		// Method not implemented.
	}

	public function get($id) {

		if (empty ($this->_data))
			return '';

		$return = null;
		$this->_get($return, $this->_data, $id);

		return $return;
	}

	protected function _get(& $return, & $data, $id) {

		if (!is_null($return))
			return;
			
		if (is_object($data) && $data->id == $id) {
			$return = $data->content;
			return;
		}
		
		if (is_object($data)) {
			foreach ($data->children as $entry) {
				$this->_get($return, $entry, $id);
			}
		}
		
		if (is_array($data)) {
			foreach ($data as $entry) {
				$this->_get($return, $entry, $id);
			}
		}
	}

	public function setResourceName($name) {

		$this->_resourceName = $name;
	}

	public function save() {

		// Method not implemented.

		return $this;
	}

	public function remove() {

		Aitsu_Db :: startTransaction();

		try {
			Aitsu_Db :: query('' .
			'delete from _syndication_resource ' .
			'where ' .
			'	sourceid = :sourceid ' .
			'	and sourceidartlang = :sourceidartlang', array (
				':sourceid' => $this->_sourceid,
				':sourceidartlang' => $this->_sourceidartlang
			));

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}
	}

	public function addIdartlang($idartlang) {

		Aitsu_Db :: query('' .
		'replace into _syndication_resource_art ' .
		'(idartlang, sourceid, sourceidartlang) ' .
		'values ' .
		'(:idartlang, :sourceid, :sourceidartlang)', array (
			':sourceid' => $this->_sourceid,
			':sourceidartlang' => $this->_sourceidartlang,
			':idartlang' => $idartlang
		));

		return $this;
	}

	public function removeIdartlang($idartlang) {

		/*
		 * Remove the specified relation.
		 */
		Aitsu_Db :: query('' .
		'delete from _syndication_resource_art ' .
		'where ' .
		'	sourceid = :sourceid ' .
		'	and sourceidartlang = :sourceidartlang ' .
		'	and idartlang = :idartlang', array (
			':sourceid' => $this->_sourceid,
			':sourceidartlang' => $this->_sourceidartlang,
			':idartlang' => $idartlang
		));

		/*
		 * Remove unused resources.
		 */
		Aitsu_Db :: query('' .
		'delete res ' .
		'from _syndication_resource res ' .
		'left join _syndication_resource_art art on res.sourceid = art.sourceid and res.sourceidartlang = art.sourceidartlang ' .
		'where art.sourceid is null');

		return $this;
	}

	public static function getResources($idart, $idlang) {

		return Aitsu_Db :: fetchAll('' .
		'select ' .
		'	rart.sourceid, ' .
		'	rart.sourceidartlang, ' .
		'	res.name, ' .
		'	src.url ' .
		'from _syndication_resource_art rart ' .
		'left join _art_lang artlang on rart.idartlang = artlang.idartlang ' .
		'left join _syndication_resource res on rart.sourceid = res.sourceid and rart.sourceidartlang = res.sourceidartlang ' .
		'left join _syndication_source src on rart.sourceid = src.sourceid ' .
		'where ' .
		'	artlang.idart = :idart ' .
		'	and artlang.idlang = :idlang ' .
		'order by ' .
		'	src.url asc, ' .
		'	res.name asc', array (
			':idart' => $idart,
			':idlang' => $idlang
		));
	}
}
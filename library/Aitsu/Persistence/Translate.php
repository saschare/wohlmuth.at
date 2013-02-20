<?php

/**
 * @author Andreas Kummer <a.kummer@wdrei.ch>
 * @copyright (c) 2013, Andreas Kummer
 */
class Aitsu_Persistence_Translate extends Aitsu_Persistence_Abstract {

    protected $_id = null;
    protected $_data = null;

    protected function __construct($id) {

        $this->_id = $id;
    }

    public static function factory($id = null) {

        static $instance = array();

        if ($id == null || !isset($instance[$id])) {
            $instance[$id] = new self($id);
        }

        return $instance[$id];
    }

    public function load() {

        if ($this->_id == null || $this->_data !== null) {
            return $this;
        }

        $this->_data = Aitsu_Db :: fetchRow('' .
                        'select * from _translate where translationid = :id', array(
                    ':id' => $this->_id
                ));

        return $this;
    }

    public function __get($key) {

        if ($this->_data === null) {
            $this->load();
        }

        if (!isset($this->_data[$key])) {
            return null;
        }

        return stripslashes($this->_data[$key]);
    }

    public function __set($key, $value) {

        if ($this->_data === null) {
            $this->_data = array();
        }

        $this->_data[$key] = $value;
    }

    public function save() {

        if (empty($this->_data)) {
            return;
        }

        Aitsu_Db :: put('_translate', 'translationid', $this->_data);
    }

    public static function getByLanguage($idlang, $searchTerm = '') {

        $searchTerm = '%' . $searchTerm . '%';

        $return = array();

        $results = Aitsu_Db :: fetchAll('' .
                        'select * from _translate ' .
                        'where ' .
                        '	idlang = :idlang ' .
                        '	and (' .
                        '		tkey like :term ' .
                        '		or tvalue like :term ' .
                        '	) ' .
                        'order by tkey asc ', array(
                    ':idlang' => $idlang,
                    ':term' => $searchTerm
                ));

        if (!$results) {
            return $return;
        }

        foreach ($results as $result) {
            $row = new self($result['translationid']);
            $row->_data = $result;
            $return[] = $row;
        }

        return $return;
    }

    public function remove() {

        Aitsu_Db :: query('' .
                'delete from _translate where translationid = :id', array(
            ':id' => $this->_id
        ));
    }

    public static function getStore($limit = null, $offset = null, $filters = null, $orders = null) {

        $filters = array_merge(is_null($filters) ? array() : $filters, array(
            (object) array(
                'clause' => 'idlang =',
                'value' => Aitsu_Registry :: get()->session->currentLanguage
            )
                ));

        return Aitsu_Db :: filter('select * from _translate', $limit, $offset, $filters, $orders);
    }

}
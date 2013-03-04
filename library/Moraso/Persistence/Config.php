<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Persistence_Config extends Aitsu_Persistence_Abstract {

    protected $_idlang = null;
    protected $_env = null;
    protected $_idclient = null;

    protected function __construct($id) {

        $this->_id = $id;
        $this->_idlang = Moraso_Util::getIdlang();
        $this->_env = Moraso_Util::getEnv();
        $this->_ebv = Moraso_Util::GetIdClient();
        
        $this->load();
    }

    public static function factory($id = null) {

        static $instance = array();

        if ($id == null || !isset($instance[$id])) {
            $instance[$id] = new self($id);
        }

        return $instance[$id];
    }

    public function load($reload = false) {

        if (!$reload && ($this->_id == null || $this->_data !== null)) {
            return $this;
        }

        $config = Moraso_Db::fetchAll('' .
                        'select ' .
                        '   client, ' .
                        '   env, ' .
                        '   identifier, ' .
                        '   value ' .
                        'from ' .
                        '   _moraso_config ' .
                        'where ' .
                        '   client =:client, ' .
                        '   env =:env ' .
                        'order by ' .
                        '   identifier asc ', array(
                    ':client' => $this->_idclient,
                    ':env' => $this->_env
        ));

        if (!$config) {
            return;
        }

        foreach ($config as $row) {

            $client = $row['client'];
            $env = $row['env'];
            $identifier = $row['identifier'];

            $this->_data[$client][$env][$identifier] = $row['value'];
        }

        return $this;
    }

    public function __get($key) {

        $keyParts = explode(':', $key);

        $client = $keyParts[0];
        $env = $keyParts[1];
        $identifier = $keyParts[2];

        if (!isset($this->_data[$client][$env][$identifier])) {
            $this->_data[$client][$env][$identifier] = array();
        }

        return $this->_data[$client][$env][$identifier];
    }

    public function __set($key, $value) {

        $keyParts = explode(':', $key);

        $client = $keyParts[0];
        $env = $keyParts[1];
        $identifier = $keyParts[2];

        $this->_data[$client][$env][$identifier] = $value;
    }

    public function __isset($key) {

        $keyParts = explode(':', $key);

        $client = $keyParts[0];
        $env = $keyParts[1];
        $identifier = $keyParts[2];

        return isset($this->_data[$client][$env][$identifier]);
    }

    public function save() {

        if (empty($this->_data)) {
            return;
        }

        Moraso_Db::startTransaction();

        try {
            Moraso_Db::query('delete from _moraso_config');

            foreach ($this->_data as $client => $envRow) {
                foreach ($envRow as $env => $identifierRow) {
                    foreach ($identifierRow as $identifier => $value) {
                        Moraso_Db::put('_moraso_config', 'null', array(
                            'client' => $client,
                            'env' => $env,
                            'identifier' => $identifier,
                            'value' => $value
                        ));
                    }
                }
            }

            Moraso_Db::commit();
        } catch (Exception $e) {
            Moraso_Db::rollback();
            throw $e;
        }

        return $this;
    }

    public function unsetValue($client, $env, $identifier) {

        if (isset($this->data[$client][$env][$identifier])) {
            unset($this->data[$client][$env][$identifier]);
        }
    }

    public function setValue($client, $env, $identifier, $value) {

        $this->_data[$client][$env][$identifier] = $value;
    }

}
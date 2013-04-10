<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Persistence_Config {

    protected function __construct($id) {
        $this->_id = $id;
        
        $this->_load();
    }

    public static function factory($id = null) {

        static $instance = array();

        if ($id == null || !isset($instance[$id])) {
            $instance[$id] = new self($id);
        }

        return $instance[$id];
    }

    private function _load($reload = false) {

        if (!$reload && ($this->_id == null || $this->_data !== null)) {
            return $this;
        }

        $config = Moraso_Db::fetchAll('' .
                        'select ' .
                        '   config, ' .
                        '   env, ' .
                        '   identifier, ' .
                        '   value ' .
                        'from ' .
                        '   _moraso_config');

        if (!$config) {
            return;
        }

        foreach ($config as $row) {

            $client = $row['config'];
            $env = $row['env'];
            $identifier = $row['identifier'];

            $this->_data[$client][$env][$identifier] = $row['value'];
        }

        return $this;
    }

    public function setValue($client, $env, $identifier, $value) {

        $this->_data[$client][$env][$identifier] = $value;
    }
    
    public function unsetValue($client, $env, $identifier) {

        if (isset($this->data[$client][$env][$identifier])) {
            unset($this->data[$client][$env][$identifier]);
        }
    }
    
    public function save() {

        if (empty($this->_data)) {
            return;
        }

        Moraso_Db::startTransaction();

        try {
            Moraso_Db::query('delete from _moraso_config');

            foreach ($this->_data as $config => $envRow) {
                foreach ($envRow as $env => $identifierRow) {
                    foreach ($identifierRow as $identifier => $value) {
                        Moraso_Db::put('_moraso_config', 'null', array(
                            'config' => $config,
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

}
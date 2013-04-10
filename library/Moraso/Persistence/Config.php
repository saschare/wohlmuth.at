<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Persistence_Config {

    protected $_data = null;

    protected function __construct() {

        $this->_load();
    }

    public static function factory() {

        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    private function _load($reload = false) {

        if (!$reload && ($this->_data !== null)) {
            return $this;
        }

        $config = Moraso_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _moraso_config');

        if (!$config) {
            return;
        }

        foreach ($config as $row) {

            $config = $row['config'];
            $env = $row['env'];
            $identifier = $row['identifier'];

            $this->_data[$config][$env][$identifier] = $row['value'];
        }

        return $this;
    }

    public function setValue($config, $env, $identifier, $value) {

        $this->_data[$config][$env][$identifier] = $value;
    }

    public function unsetValue($config, $env, $identifier) {

        if (isset($this->data[$config][$env][$identifier])) {
            unset($this->data[$config][$env][$identifier]);
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
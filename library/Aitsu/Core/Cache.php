<?php

/**
 * aitsu cache system
 *
 * @author Christian Kehres, webtischlerei.de
 * @author Andreas Kummer, w3concepts AG
 * 
 * {@id $Id: Cache.php 18199 2010-08-18 14:57:07Z akm $}
 */
class Aitsu_Core_Cache {

    protected $cache = null;
    protected $memcached = null;
    protected $id = null;
    protected $disabled = false;
    protected $lifetime;

    protected function __construct() {

        $this->lifetime = 60 * 60 * 24 * 365;

        $frontendOptions = array(
            'lifetime' => $this->lifetime,
            'automatic_serialization' => false
        );

        if (!isset(Aitsu_Registry :: get()->config->memcached->enable) || Aitsu_Registry :: get()->config->memcached->enable == false || (isset(Aitsu_Registry :: get()->config->memcached->simultaneosly) && Aitsu_Registry :: get()->config->memcached->simultaneosly == true)) {
            $cache_dir = APPLICATION_PATH . '/data/cache/';

            if (!is_dir($cache_dir)) {
                mkdir($cache_dir, 0777, true);
            }

            $backendOptions = array(
                'cache_dir' => $cache_dir
            );

            $this->cache = Zend_Cache :: factory('Output', 'File', $frontendOptions, $backendOptions);
        }

        if (isset(Aitsu_Registry :: get()->config->memcached->enable) && Aitsu_Registry :: get()->config->memcached->enable) {
            $server = Aitsu_Registry :: get()->config->memcached->server->toArray();
            $backendOptions = array(
                'server' => $server
            );
            $this->memcached = Zend_Cache :: factory('Output', 'Memcached', $frontendOptions, $backendOptions);
        }
    }

    public static function getInstance($id, $disable = false) {

        static $instance = array();

        if (!isset($instance[$id])) {
            $instance[$id] = new self($id, $disable);
        }

        $instance[$id]->id = $id;
        $instance[$id]->disabled = $disable;

        return $instance[$id];
    }

    public function load() {

        if ($this->disabled) {
            return false;
        }

        if ($this->memcached != null && $this->memcached->test($this->id)) {
            return $this->memcached->load($this->id);
        }

        if ($this->cache != null) {
            return $this->cache->load($this->id);
        }

        return false;
    }

    public function isValid() {

        if ($this->disabled) {
            return false;
        }

        if ($this->memcached != null && $this->memcached->test($this->id)) {
            return true;
        }

        if ($this->cache != null) {
            return $this->cache->test($this->id);
        }

        return false;
    }

    public function save($data, $tags = null) {

        if ($this->disabled) {
            return;
        }

        if ($this->memcached != null) {
            $this->memcached->save($data, $this->id, array(), 60 * 60 * 24 * 30);
        }

        if ($this->cache != null) {
            if ($tags == null) {
                $this->cache->save($data, $this->id, array(), $this->lifetime);
            } else {
                $this->cache->save($data, $this->id, $tags, $this->lifetime);
            };
        }
    }

    public function setLifetime($newLifetime) {

        $this->lifetime = $newLifetime;
    }

    public function remove() {

        if ($this->memcached != null) {
            $this->memcached->remove($this->id);
        }

        if ($this->cache != null) {
            $this->cache->remove($this->id);
        }
    }

    public function clean($tags = null, $mode = Zend_Cache :: CLEANING_MODE_MATCHING_ANY_TAG) {

        if ($this->memcached != null) {
            $this->memcached->clean(Zend_Cache :: CLEANING_MODE_ALL);
        }

        if ($this->cache != null) {
            if (empty($tags)) {
                return $this->cache->clean(Zend_Cache :: CLEANING_MODE_ALL);
            }

            $this->cache->clean($mode, $tags);
        }
    }

    public function enable() {

        $this->disabled = false;
    }

}
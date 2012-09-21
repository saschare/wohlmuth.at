<?php


/**
 * RSS feed generator.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Generator.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Feed_Rss_Generator {
	
	protected $id;
	protected $channel;
	
	protected function __construct($id) {
		
		$this->id = $id;
	}
	
	public static function getInstance($id, $channel = array()) {
		
		static $instance = array();
		
		if (!isset($instance[$id])) {
			$instance[$id] = new self($id);
			$instance[$id]->channel = $channel;
		}
		
		return $instance[$id];
	}
	
	public function getAsFeed($data) {
		
		$xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><rss version="2.0"></rss>');
		
		$channel = $xml->addChild('channel');
		
		foreach ($channel as $field => $value) {
			$channel->addChild($field, $value);
		}
		
		foreach ($data as $record) {
			$item = $channel->addChild('item');
			foreach ($record as $field => $value) {
				$item->addChild($field, $value);
			}
		}
		
		return $xml->asXML();
	}
}
<?php


/**
 * Array parser for aitsu.
 * @version $Id: Array.php 16090 2010-04-22 19:44:08Z akm $
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2009, w3concepts AG
 */

class Aitsu_Parser_Array {
	
	protected $value = array();
	
	protected function __construct($valueToBeParsed) {
		
		$this->value = $valueToBeParsed;
	}
	
	public static function factory($valueToBeParsed) {
		
		$instance = new self($valueToBeParsed);
		
		return $instance;
	}
	
	public function setRowTag($tag, $attributes = null) {
		
		$this->tag['row']['tag'] = $tag;
		$this->tag['row']['attributes'] = $attributes;
		
		return $this;
	}
	
	public function setCellTag($tag, $attributes = null) {
		
		$this->tag['cell']['tag'] = $tag;
		$this->tag['cell']['attributes'] = $attributes;
		
		return $this;
	}
	
	public function setFirstRowTag($tag, $attributes = null) {
		
		$this->tag['firstrow']['tag'] = $tag;
		$this->tag['firstrow']['attributes'] = $attributes;
		
		return $this;
	}

	public function setFirstRowCellTag($tag, $attributes = null) {
		
		$this->tag['firstcell']['tag'] = $tag;
		$this->tag['firstcell']['attributes'] = $attributes;
		
		return $this;
	}
	
	public function parse() {
		
		$out = '';

		for ($i = 0; $i < count($this->value); $i++) {
			if ($i == 0) {
				if (isset($this->tag['firstrow'])) {
					$rowTag = $this->tag['firstrow'];	
				} else {
					$rowTag = $this->tag['row'];
				}
				if (isset($this->tag['firstcell'])) {
					$cellTag = $this->tag['firstcell'];	
				} else {
					$cellTag = $this->tag['cell'];
				}				
			} else {
				$rowTag = $this->tag['row'];
				$cellTag = $this->tag['cell'];
			}
			$out .= $this->getTag($rowTag, $i, true);
			
			for ($j = 0; $j < count($this->value[$i]); $j++) {
				$out .= $this->getTag($cellTag, $j, true);
				$out .= $this->value[$i][$j];
				$out .= $this->getTag($cellTag, $j, false);
			}
			
			$out .= $this->getTag($rowTag, $i, false);
		}
		
		return $out;		
	}
	
	protected function getTag($tag, $position, $start = false) {
		
		$out = '<';
		
		if (!$start) {
			$out .= '/' . $tag['tag'] . '>';
			return $out;
		}
		
		$out .= $tag['tag'];
		
		if ($tag['attributes'] != null) {
			foreach ($tag['attributes'] as $key => $value) {
				$values = explode('|', $value);
				$out .= ' ' . $key . '="' . $values[$position % count($values)] . '"';
			}
		}
		
		$out .= '>';
		
		return $out;
	}
}
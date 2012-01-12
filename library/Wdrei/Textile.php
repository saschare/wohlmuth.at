<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, kummer
 */
class Wdrei_Textile {
	
	protected $_text = '';

	protected function __construct() {
	}

	public static function textile($text) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}
		
		$instance->_text = $text;
		$instance->_transform();
		
		// return $instance->_text;

		return Thresholdstate_Textile :: textile($instance->_text);
	}

	protected function _transform() {

		foreach (get_class_methods($this) as $transformation) {
			if (substr($transformation, 0, strlen('_em')) == '_em') {
				call_user_func(array (
					$this,
					$transformation
				));
			}
		}
	}
	
	/**
	 * Shortcodes.
	 * Example: .sc(Shortcode:Index)
	 */
	protected function _emShortcode() {

		$this->_text = preg_replace('/\\.sc\\(([^\\)]*)\\)/', "_[$1]", $this->_text);
	}
	
	/**
	 * Bold text.
	 * Example: **bold**
	 */
	protected function _emBold() {
		
		$this->_text = preg_replace('/\\*{2}([^(?:\\*{2})\\n\\r]{1,})\\*{2}/m', "<b>$1</b>", $this->_text);
	}
	
	/**
	 * Strong text.
	 * Example: *strong*
	 */
	protected function _emStrong() {
		
		$this->_text = preg_replace('/\\*([^\\*\\n\\r]{1,})\\*/m', "<b>$1</b>", $this->_text);
	}
	
	/**
	 * Bullet lists.
	 */
	protected function _emBulletList() {
		
		if (preg_match_all('/\\*\\s*.*?(?:(?:\\n\\r?){2,}|$)/s', $this->_text, $matches) == 0) {
			return;
		}
		
		foreach ($matches[0] as $match) {
			$replacement = preg_replace('/^\\*\\s*(.*)/m', " <li>$1</li>", $match);
			$this->_text = str_replace($match, '<ul>' . $replacement . '</ul>' . "\n\n", $this->_text);
		}
	}
	
	/**
	 * Ordered lists.
	 */
	protected function _emOrderedList() {
		
		if (preg_match_all('/\\#\\s.*?(?:(?:\\n\\r?){2,}|$)/s', $this->_text, $matches) == 0) {
			return;
		}
		
		foreach ($matches[0] as $match) {
			$replacement = preg_replace('/^\\#\\s*(.*)/m', " <li>$1</li>", $match);
			$this->_text = str_replace($match, '<ol>' . $replacement . '</ol>' . "\n\n", $this->_text);
		}
	}
	
}
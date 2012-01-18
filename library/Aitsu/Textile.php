<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, kummer
 */
class Aitsu_Textile extends Wdrei_Textile {
	
	/**
	 * Embed shortcodes into a div, if they represent a block.
	 */
	protected function _emEmbedShortcode() {
		
		$class = '';
		
		if (Aitsu_Application_Status :: isEdit()) {
			$class = ' class="shortcodeBlock"';
		}
		
		$this->_text = preg_replace('/((?:^|(?:\\n\\r?){2,}))(\\.sc\\([^\\)]*\\))((?:(?:\\n\\r?){2,}|$))/s', "$1<div$class>$2</div>$3", $this->_text);
	}	
}
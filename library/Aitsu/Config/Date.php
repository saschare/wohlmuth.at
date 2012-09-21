

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 28.01.2011
 * Please use class Aitsu_Content_Config_Date instead.
 */
class Aitsu_Config_Date extends Aitsu_Content_Config_Abstract {
	
	public function getTemplate() {

		return 'Date.phtml';
	}
	
	public static function set($index, $name, $label, $fieldset = '') {
		
		$instance = new self($index, $name);
		
		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'date';
		
		return $instance->currentValue();
	}
}
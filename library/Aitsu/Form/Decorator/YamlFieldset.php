<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: YamlFieldset.php 18744 2010-09-14 08:35:49Z akm $}
 */

class Aitsu_Form_Decorator_YamlFieldset extends Zend_Form_Decorator_Abstract {
	
	public function render($content) {

		$element = $this->getElement();

		$type = array (
			'Zend_Form_Element_Checkbox' => 'check',
			'Zend_Form_Element_MultiCheckbox' => 'check',
			'Zend_Form_Element_Multiselect' => 'select',
			'Zend_Form_Element_Password' => 'text',
			'Zend_Form_Element_Radio' => 'check',
			'Zend_Form_Element_Select' => 'select',
			'Zend_Form_Element_Text' => 'text',
			'Zend_Form_Element_Textarea' => 'text'
		);

		if (isset ($type[$element->getType()])) {
			$type = $type[$element->getType()];
		} else {
			$type = 'text';
		}

		$error = $element->hasErrors() ? ' error' : '';

		$messages = '';
		if ($element->hasErrors()) {
			$messages .= '<ul class="message">';
			foreach ($element->getMessages() as $message) {
				$messages .= '<li>' . $message . '</li>';
			}
			$messages .= '</ul>';
		}
	
		$return = '<fieldset class="rounded-corners"><legend>' . Zend_Registry::get('Zend_Translate')->translate($element->getLabel()) . '</legend>';
		$return .= '<div class="type-' . $type . $error . '">' . $messages . $content . '</div>';
		return $return . '</fieldset>';
	}
}
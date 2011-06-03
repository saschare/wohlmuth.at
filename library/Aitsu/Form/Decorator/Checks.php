<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Yaml.php 18472 2010-08-31 14:54:59Z akm $}
 */

class Aitsu_Form_Decorator_Checks extends Zend_Form_Decorator_ViewHelper {

	protected $idCounter = 0;
	protected $_id;

	public function render($content) {

		$element = $this->getElement();

		if (method_exists($element, 'getMultiOptions')) {
			$element->getMultiOptions();
		}

		$helper = $this->getHelper();
		$separator = $this->getSeparator();
		$value = $this->getValue($element);
		$attribs = $this->getElementAttribs();
		$name = $element->getFullyQualifiedName();
		$this->_id = $element->getId();

		if (is_array($attribs['options']) && count($attribs['options']) > 0) {
			$attribChunks = array_chunk($attribs['options'], ceil(count($attribs['options']) / 2), true);

			$elementContent = '<div class="subcolumns"><div class="c50l"><div class="subcl">';
			$elementContent .= $this->_render($element->getType(), $name, $value, $attribChunks[0]) . '</div></div>';
			$elementContent .= '<div class="c50r"><div class="subcl">';
			$elementContent .= $this->_render($element->getType(), $name, $value, isset($attribChunks[1]) ? $attribChunks[1] : null) . '</div></div>';
			$elementContent .= '</div>';

			return $elementContent;
		}
		
		return '';
	}

	protected function _render($type, $name, $values, $attribs) {

		$return = '';
		
		if (!is_array($attribs) || count($attribs) == 0) {
			return '';
		}

		foreach ($attribs as $value => $key) {
			$this->idCounter++;
			$return .= '<div class="type-check">';
			if ($type == 'Zend_Form_Element_MultiCheckbox') {
				$checked = is_array($values) && in_array($value, $values) ? ' checked="checked"' : '';
				$return .= '<input type="checkbox" name="' . $name . '" id="' . $this->_id . '-' . $this->idCounter . '" value="' . $value . '"' . $checked . ' />';
				$return .= '&nbsp;<label for="' . $this->_id . '-' . $this->idCounter . '" >' . $key . '</label>';
			}
			elseif ($type == 'Zend_Form_Element_Radio') {
				$checked = $value == $values ? ' checked="checked"' : '';
				$return .= '<input type="radio" name="' . $name . '" id="' . $this->_id . '-' . $this->idCounter . '" value="' . $value . '"' . $checked . ' />';
				$return .= '&nbsp;<label for="' . $this->_id . '-' . $this->idCounter . '">' . $key . '</label>';
			}
			$return .= '</div>';
		}

		return $return;
	}
}
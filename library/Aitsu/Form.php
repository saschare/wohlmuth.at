<?php


/**
 * Extension of the Zend_Form class.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Form.php 18769 2010-09-14 19:02:01Z akm $}
 */

class Aitsu_Form extends Zend_Form {

	public function __construct($options = null) {

		$this->addPrefixPath('Aitsu_Form_Element', 'Aitsu/Form/Element/', 'element');
		$this->addElementPrefixPath('Aitsu_Validate', 'Aitsu/Validate/', 'validate');

		parent :: __construct($options);

		$this->_setYamlDecorators();
	}

	public function setValues($values) {
		
		if (!is_array($values)) {
			return;
		}

		foreach ($values as $key => $value) {
			if (isset ($this->_elements[$key])) {
				$this->_elements[$key]->setValue($value);
			}
		}
	}

	protected function _setYamlDecorators() {

		/*
		 * Set class of the form to yform.
		 */
		$class = empty($this->_attribs['class']) ? '' : ' ' . $this->_attribs['class'];
		$type = !empty($this->_attribs['type']) && $this->_attribs['type'] == 'normal' ? '' : ' ajax';
		$this->setOptions(array (
			'attribs' => array (
				'class' => 'yform full' . $type . $class
			)
		));

		/*
		 * Set form decorators.
		 */
		$this->setDecorators(array (
			'FormElements',
			'Form'
		));

		/*
		 * Set YAML decorator with fieldsets for all elements.
		 */
		$this->setElementDecorators(array (
			'ViewHelper',
			new Aitsu_Form_Decorator_YamlFieldset()
		));

		/*
		 * Set YAML (without fieldsets) for elements belonging
		 * to a display group.
		 */
		foreach ($this->getDisplayGroups() as $group) {
			foreach ($group->getElements() as $element) {
				$element->setDecorators(array (
					'ViewHelper',
					'Label',
					new Aitsu_Form_Decorator_Yaml()
				));
			}
		}

		foreach ($this->_elements as $key => $element) {
			if (in_array($element->getType(), array (
					'Zend_Form_Element_MultiCheckbox',
					'Zend_Form_Element_Radio'
				))) {
				/*
				 * Wrap radio buttons and checkboxes with a four
				 * column layout and a fieldset.
				 */
				$element->setDecorators(array (
					new Aitsu_Form_Decorator_Checks(),
					new Aitsu_Form_Decorator_YamlFieldset()
				));
			}
			elseif ($element->getType() == 'Zend_Form_Element_Hidden') {
				/*
				 * Remove decorators for hidden fields.
				 */
				$element->setDecorators(array (
					'ViewHelper'
				));
			}
			elseif ($element->getType() == 'Aitsu_Form_Element_CollapsableTree') {
				/*
				 * Set special html decoration.
				 */
				$element->setDecorators(array (
					array (
						'HtmlTag',
						array (
							'tag' => 'div',
							'class' => 'type-special'
						)
					),
					'Fieldset'
				));
			}
			elseif (in_array($element->getType(), array (
				'Zend_Form_Element_Submit',
				'Zend_Form_Element_Button'
			))) {
				/*
				 * Wrap buttons with a simple div.
				 */
				$element->setDecorators(array (
					'ViewHelper'
				));
			}
		}

		$this->setDisplayGroupDecorators(array (
			'FormElements',
			'Fieldset'
		));

		if ($this->getDisplayGroup('buttons') != null) {
			$this->getDisplayGroup('buttons')->setDecorators(array (
				'FormElements',
				array (
					'HtmlTag',
					array (
						'tag' => 'div',
						'class' => 'type-button'
					)
				)
			));
		}
	}
}
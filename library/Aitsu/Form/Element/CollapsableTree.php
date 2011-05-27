<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id$}
 */

class Aitsu_Form_Element_CollapsableTree extends Zend_Form_Element_Multi {
	
	protected $_values;

	public function render(Zend_View_Interface $view = null) {
		
		if ($this->_isPartialRendering) {
			return '';
		}

		if (null !== $view) {
			$this->setView($view);
		}
		
		$this->_values = !is_array($this->getValue()) ? array() : $this->getValue();
		$attribs = $this->getAttribs();
		$content = $this->_renderContent($attribs['options'], true);
		
		$this->removeDecorator('ViewHelper');
		
		foreach ($this->getDecorators() as $decorator) {
			$decorator->setElement($this);
			$content = $decorator->render($content);
		}
		
		return $content;
	}
	
	protected function _renderContent($tree, $first = false) {
		
		$return = '';
		
		if ($first) {
			$return .= '<ul class="checkboxtree">';
		} else {
			$return .= '<ul>';
		}
		foreach ($tree as $level) {
			$checked = in_array($level['id'], $this->_values) ? ' checked="checked"' : '';
			$return .= '<li><input type="checkbox" name="" ' . $checked . '/>';
			$return .= $level['name'];
			if (isset($level['children'])) {
				$return .= $this->_renderContent($level['children']);
			}
			$return .= '</li>';
		}
		$return .= '</ul>';
		
		return $return;
	}
}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Cattree.php 18600 2010-09-08 12:39:47Z akm $}
 */

class Aitsu_Form_Element_Cattree extends Zend_Form_Element_Multi {
	
	protected $_values;

	public function render(Zend_View_Interface $view = null) {
		
		if ($this->_isPartialRendering) {
			return '';
		}

		if (null !== $view) {
			$this->setView($view);
		}
		
		$this->removeDecorator('ViewHelper');
		
		$content = '<div class="catTree"></div>';
		
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        
        return $content;
	}
	
}
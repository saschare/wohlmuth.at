<?php


/**
 * Date element as an extension of the Zend_Form_Element.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Date.php 18432 2010-08-30 12:45:41Z akm $}
 */

class Aitsu_Form_Element_Date extends Zend_Form_Element_Text {

	public function init() {

		$this->addValidator('Datetime');
		$this->setAttrib('class', 'date');
	}
}
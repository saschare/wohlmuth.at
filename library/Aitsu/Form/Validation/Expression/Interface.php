<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

interface Aitsu_Form_Validation_Expression_Interface extends Aitsu_Form_Validation_Expression_Basic_Interface {

	public function isValid(& $value);

}
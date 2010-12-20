<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright w3concepts AG
 */

abstract class Aitsu_Forms_Validator {
	
	abstract public function isValid();
	
	abstract public function getMessage();
}
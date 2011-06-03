<?php

/**
 * @author Dominik Graf, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
interface Aitsu_Event_Interface {
	public static function raise($signature, $context);
}
<?php


/**
 * Transformation.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id$}
 */

class Aitsu_Transformation {
	
	public static function transform(& $content) {
		
		foreach (Aitsu_Registry :: get()->config->transformation->toArray() as $transformation => $enabled) {
			if ($enabled) {
				$obj = call_user_func(array (
					$transformation,
					'getInstance'
				));
				$content = $obj->getContent($content);
			}
		}
	}
}
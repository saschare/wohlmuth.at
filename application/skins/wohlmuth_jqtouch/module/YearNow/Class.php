<?php

class Skin_Module_YearNow_Class extends Aitsu_Module_Abstract {

	public static function init($context) {

		$index = $context['index'];
		
		Aitsu_Content_Edit :: noEdit('YearNow', true);

		return date("Y"); 
	}

}
?>
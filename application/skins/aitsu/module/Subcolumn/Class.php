<?php

class Skin_Module_Subcolumn_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();

		$index = $context['index'];

		$output = '';
		if (!$instance->_get('Subcolumn' . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {

			$template = Aitsu_Content_Config_Radio :: set($index, 'SubcolumnTemplate', '', $instance->_getTemplates(), 'Template');
			
			if (empty ($template)) {
				$template = '100';
			}

			$view = $instance->_getView();
		
			$view->index = $index;

			$id = Aitsu_Content_Config_Text :: set($index, 'TagAImgId', 'id', 'Additional tag attributes');
			$class = Aitsu_Content_Config_Text :: set($index, 'TagAImgClass', 'class', 'Additional tag attributes');
			$style = Aitsu_Content_Config_Text :: set($index, 'TagAImgStyle', 'style', 'Additional tag attributes');
			
			$view->id = empty($id) ? NULL : ' id="' . $id . '"';
			$view->class = empty($class) ? NULL : ' class="subcolumns ' . $class . '"';
			$view->style = empty($style) ? NULL : ' style="' . $style . '"';
			
			if (Aitsu_Registry :: isEdit()) {
				$output .= '<div class="padding:5px 0;">// Subcolumn ' . $index . ' //';
			} 

			$output .= $view->render($template . '.phtml');

			if (Aitsu_Registry :: isEdit()) {
				$output .= '</div>';
			} 
			
			$instance->_save($output, 'eternal');

		}

		return $output;
	}
}
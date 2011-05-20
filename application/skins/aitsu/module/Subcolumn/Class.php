<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_Subcolumn_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Subcolumn',
			'description' => Aitsu_Translate :: translate('Inserts a subcolumn'),
			'type' => 'Layout',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db9401b-9370-4da0-96b7-0bf150431368'
		);
	}
	
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

			$id = Aitsu_Content_Config_Text :: set($index, 'SubcolumnId', 'id', 'Additional tag attributes');
			$class = Aitsu_Content_Config_Text :: set($index, 'SubcolumnClass', 'class', 'Additional tag attributes');
			$style = Aitsu_Content_Config_Text :: set($index, 'SubcolumnStyle', 'style', 'Additional tag attributes');
			
			$view->id = empty($id) ? NULL : ' id="' . $id . '"';
			$view->class = empty($class) ? NULL : ' class="subcolumns ' . $class . '"';
			$view->style = empty($style) ? NULL : ' style="' . $style . '"';
			
			if (Aitsu_Registry :: isEdit()) {
				$output .= '<div class="padding:5px 0;">:: Subcolumn ' . $index . ' ::';
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
<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_TagAImg_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'TagAImg',
			'description' => Aitsu_Translate :: translate('Inserts a link image'),
			'type' => 'Tags',
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

        $display = Aitsu_Content_Config_Select :: set($index, 'TagAImgDisplay', 'Display', array('block' => 'block', 'inline' => 'inline'), 'Backend options');

		if($display == 'inline') {
		    Aitsu_Content_Edit :: isBlock('TagAImg', false);
		}

		$output = '';
		if (!$instance->_get('TagAImg' . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {
		
			$template = Aitsu_Content_Config_Radio :: set($index, 'TagAImgTemplate', '', $instance->_getTemplates(), 'Template');
			
			if (empty ($template)) {
				$template = 'index';
			}

			$view = $instance->_getView();

	        $href = Aitsu_Content_Config_Link :: set($index, 'TagALink', 'Href', 'Link');
			
        	$images = Aitsu_Content_Config_Media :: set($index, 'TagAImgMedia', 'Media');
			$images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);

			$width = Aitsu_Content_Config_Text :: set($index, 'TagAImgWidth', 'width', 'Additional image attributes');
			$height = Aitsu_Content_Config_Text :: set($index, 'TagAImgHeight', 'height', 'Additional image attributes');
			$transform = Aitsu_Content_Config_Radio :: set($index, 'TagAImgTransform', 'Transform', array('Scale to fit' => '0', 'Cut to size' => '1', 'Cut to focus' => '2'), 'Choose image transformation');
			
			$alt = Aitsu_Content_Config_Text :: set($index, 'TagAImgAlt', 'alt', 'Additional image meta');
			$title = Aitsu_Content_Config_Text :: set($index, 'TagAImgTitle', 'title', 'Additional image meta');
	        $target = Aitsu_Content_Config_Select :: set($index, 'TagAImgTarget', 'target', array('none' => 'none', '_blank' => '_blank', '_self' => '_self', '_top' => '_top','_parent' => '_parent'), 'Additional link attributes');
			
			$id = Aitsu_Content_Config_Text :: set($index, 'TagAImgId', 'id', 'Additional tag attributes');
			$class = Aitsu_Content_Config_Text :: set($index, 'TagAImgClass', 'class', 'Additional tag attributes');
			$style = Aitsu_Content_Config_Text :: set($index, 'TagAImgStyle', 'style', 'Additional tag attributes');
			
			$view->index = $index;
			$view->images = $images;
			$view->href = $href;
			
			$view->width = empty($width) ? 1280 : $width;
			$view->height = empty($height) ? 1280 : $height;
			$view->transform = empty($transform) ? 0 : $transform;
			
			$view->alt = empty($alt) ? NULL : ' alt="' . $alt . '"';
			$view->title = empty($title) ? NULL : ' title="' . $title . '"';
			$view->title = empty($title) ? NULL : ' title="' . $title . '"';
			
			$view->id = empty($id) ? NULL : ' id="' . $id . '"';
			$view->class = empty($class) ? NULL : ' class="' . $class . '"';
			$view->style = empty($style) ? NULL : ' style="' . $style . '"';
			
			if (count($view->images) == 0 || !$href) {
				if (Aitsu_Application_Status :: isEdit()) {
					$output = '| TagAImg :: ' . $index . ' |';
				} else {
					$output = '';
				}
			} else {
				$output = $view->render($template . '.phtml');
				$instance->_save($output, 'eternal');
			}
			
		}

		return $output;
	}
}
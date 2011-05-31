<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_TagImg_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'TagImg',
			'description' => Aitsu_Translate :: translate('Inserts a html image tag'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.1',
			'status' => 'stable',
			'url' => null,
			'id' => '4db9401b-9370-4da0-96b7-0bf150431368'
		);
	}
	
	public static function init($context) {

		$instance = new self();

		$index = $context['index'];

        $display = Aitsu_Content_Config_Select :: set($index, 'ImgDisplay', 'Display', array('block' => 'block', 'inline' => 'inline'), 'Backend options');

		if($display == 'inline') {
		    Aitsu_Content_Edit :: isBlock('Img', false);
		}

		$output = '';
		if (!$instance->_get('Img' . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {

			$template = Aitsu_Content_Config_Radio :: set($index, 'ImgTemplate', '', $instance->_getTemplates(), 'Template');
			
			if (empty ($template)) {
				$template = 'index';
			}

			$view = $instance->_getView();

			$images = Aitsu_Content_Config_Media :: set($index, 'ImgMedia', 'Media');
			$images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);

			$transform = Aitsu_Content_Config_Radio :: set($index, 'ImgTransform', 'Transform', array('Scale to fit' => '0', 'Cut to size' => '1', 'Cut to focus' => '2'), 'Choose image transformation');
			$width = Aitsu_Content_Config_Text :: set($index, 'ImgWidth', 'width', 'Additional image attributes');
			$height = Aitsu_Content_Config_Text :: set($index, 'ImgHeight', 'height', 'Additional image attributes');

			$alt = Aitsu_Content_Config_Text :: set($index, 'ImgAlt', 'alt', 'Additional image meta');
			$title = Aitsu_Content_Config_Text :: set($index, 'TagImgTitle', 'title', 'Additional image meta');

			$id = Aitsu_Content_Config_Text :: set($index, 'ImgId', 'id', 'Additional tag attributes');
			$class = Aitsu_Content_Config_Text :: set($index, 'ImgClass', 'class', 'Additional tag attributes');
			$style = Aitsu_Content_Config_Text :: set($index, 'ImgStyle', 'style', 'Additional tag attributes');

			$thumbTransform = Aitsu_Content_Config_Radio :: set($index, 'ImgThumbTransform', 'Transform', array('Scale to fit' => '0', 'Cut to size' => '1', 'Cut to focus' => '2'), 'Choose thumb transformation');
			$thumbWidth = Aitsu_Content_Config_Text :: set($index, 'ImgThumbWidth', 'width', 'Additional thumb attributes');
			$thumbHeight = Aitsu_Content_Config_Text :: set($index, 'ImgThumbHeight', 'height', 'Additional thumb attributes');
			
			$view->index = $index;
			$view->images = $images;

			$view->transform = !empty($transform) ? $transform : 0;
			$view->width = !empty($width) ? $width : 150;
			$view->height = !empty($height) ? $height : 100;
			
			$view->alt = empty($alt) ? NULL : ' alt="' . $alt . '"';
			$view->title = empty($title) ? NULL : ' title="' . $title . '"';

			$view->id = empty($id) ? NULL : ' id="' . $id . '"';
			$view->class = empty($class) ? NULL : ' class="' . $class . '"';
			$view->style = empty($style) ? NULL : ' style="' . $style . '"';
			
			$view->thumbTransform = !empty($thumbTransform) ? $thumbTransform : 0;
			$view->thumbWidth = !empty($thumbWidth) ? $thumbWidth : 30;
			$view->thumbHeight = !empty($thumbHeight) ? $thumbHeight : 30;
			
			if (count($view->images) == 0) {
				if (Aitsu_Application_Status :: isEdit()) {
					$output = '| TagImg :: ' . $index . ' |';
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
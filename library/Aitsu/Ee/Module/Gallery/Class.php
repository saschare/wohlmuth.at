<?php


/**
 * Jon Design's SmoothGallery V 2.1
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17819 2010-07-29 09:46:46Z akm $}
 */

class Aitsu_Ee_Module_Gallery_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Gallery',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns the specified images (configuration) as a gallery specified in the particular template.'),
			'type' => array (
				'List',
				'Image'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725367-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		/*
		 * Notice:
		 * As the template injects javascript to the bottom of the
		 * html code, the caching of the output would prevent the
		 * javascript code from beeing injected, as this command is
		 * made in the template rendering phase.
		 * Rather than caching the output, we cache the view object
		 * and the necessary data for rendering (template).
		 */

		$instance = new self();

		$index = $context['index'];

		$output = '';
		if ($instance->_get('Gallery_' . $context['index'], $output)) {
			$data = unserialize($output);
			return $data->view->render($data->template . '.phtml');
		}

		$images = Aitsu_Ee_Config_Images :: set($index, 'Images', '', Aitsu_Translate :: _('Choose images to be shown'));
		$gallery = Aitsu_Ee_Config_Radio :: set($index, 'Gallery', '', array (
			'Standard' => 'index'
		), 'Gallery');

		if (empty ($gallery)) {
			$gallery = 'index';
		}

		$view = $instance->_getView();
		$view->images = $images;

		if (count($images) == 0) {
			return '<div><strong>Gallery :: No images selected.</strong></div>';
		}

		$output = $view->render($gallery . '.phtml');

		$instance->_save(serialize((object) array (
			'view' => $view,
			'template' => $gallery
		)), 'eternal');

		return $output;
	}
}
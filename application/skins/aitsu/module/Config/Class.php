<?php

/**
 * @author Frank Ammari, meine experten GbR
 * @copyright Copyright &copy; 2010, meine experten GbR
 * 
 * Example Configurations
 * 
 */

class Skin_Module_Config_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Config',
			'description' => Aitsu_Translate :: translate('Show a set of example module configurations'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'meine experten GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db93e07-4274-4455-9d30-096f50431368'
		);
	}
	
	public static function init($context) {

		$instance = new self();

		$index = $context['index'];
		Aitsu_Content_Edit :: isBlock(false);

		$copy = Aitsu_Content_Html :: get('Copy');
		$config = array();
		$config['radio'] = Aitsu_Content_Config_Radio :: set($index, 'Radio', '', array (
			'key1' => 'value1',
			'key2' => 'value2'
		), 'Radio buttons');
		$config['checkbox'] = Aitsu_Content_Config_Checkbox :: set($index, 'Checkbox_1', '', array (
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3',
			'key4' => 'value4',
			'key5' => 'value5',
			'key6' => 'value6',
			'key7' => 'value7',
			'key8' => 'value8',
			'key9' => 'value9'
		), 'Checkboxes');
		$config['timeStart'] = Aitsu_Content_Config_Date :: set($index, 'start_time', 'Start', 'Periode');
		$config['timeEnd'] = Aitsu_Content_Config_Date :: set($index, 'end_time', 'Ende', 'Periode');
		$config['text1'] = Aitsu_Content_Config_Text :: set($index, 'remark1', 'Text', 'Periode');
		$config['text2'] = Aitsu_Content_Config_Text :: set($index, 'remark2', 'Text', 'Test');
		$config['combo'] = Aitsu_Content_Config_Select :: set($index, 'Select_1', 'Select (Combo)', array (
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3',
			'key4' => 'value4',
			'key5' => 'value5',
			'key6' => 'value6',
			'key7' => 'value7',
			'key8' => 'value8',
			'key9' => 'value9'
		), 'Test');
		$config['link'] = Aitsu_Content_Config_Link :: set($index, 'link', 'Quelle', 'Link');

		$environment['idcat'] = Aitsu_Registry :: get()->env->idcat; 
		$environment['idart'] = Aitsu_Registry :: get()->env->idart;
		$environment['idartlang'] = Aitsu_Registry :: get()->env->idartlang;
		$environment['idlang'] = Aitsu_Registry :: get()->env->idlang;
		$environment['edit'] = Aitsu_Registry :: get()->env->edit;
		$environment['locale'] = Aitsu_Registry :: get()->env->locale;
		
		$view = $instance->_getView();
		$images = Aitsu_Content_Config_Media :: set($index, 'Image_Media', 'Media');
		$view->images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);
		$view->config = $config;
		$view->environment = $environment;
			
		if (Aitsu_Registry :: isEdit()) {
			$output = '// Config ' . $index . ' //';
		}

		$output = $view->render('index.phtml');

		return $output;
	}
}
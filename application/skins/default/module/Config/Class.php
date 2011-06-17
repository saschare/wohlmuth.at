<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
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
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.1',
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
		$contentConfig = array();
		$contentConfig['radio'] = Aitsu_Content_Config_Radio :: set($index, 'Radio', '', array (
			'key1' => 'value1',
			'key2' => 'value2'
		), 'Radio buttons');
		$contentConfig['checkbox'] = Aitsu_Content_Config_Checkbox :: set($index, 'Checkbox_1', '', array (
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
		$contentConfig['timeStart'] = Aitsu_Content_Config_Date :: set($index, 'start_time', 'Start', 'Periode');
		$contentConfig['timeEnd'] = Aitsu_Content_Config_Date :: set($index, 'end_time', 'Ende', 'Periode');
		$contentConfig['text1'] = Aitsu_Content_Config_Text :: set($index, 'remark1', 'Text', 'Periode');
		$contentConfig['text2'] = Aitsu_Content_Config_Text :: set($index, 'remark2', 'Text', 'Test');
		$contentConfig['combo'] = Aitsu_Content_Config_Select :: set($index, 'Select_1', 'Select (Combo)', array (
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
		$contentConfig['link'] = Aitsu_Content_Config_Link :: set($index, 'link', 'Quelle', 'Link');

		$environment = Aitsu_Registry :: get()->env;
		
		//Dangerous: With this you could print passwords at frontend
		$config = Aitsu_Registry :: get()->config;
		
		$view = $instance->_getView();
		$images = Aitsu_Content_Config_Media :: set($index, 'Image_Media', 'Media');
		$view->copy = $copy;
		$view->contentConfig = $contentConfig;
		$view->images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);
		$view->environment = $environment;
		$view->config = $config;
		
		if (Aitsu_Application_Status :: isEdit()) {
			$output = '| Config :: ' . $index . ' |';
		}

		$output = $view->render('index.phtml');

		return $output;
	}
}
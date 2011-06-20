<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_Twitter_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Twitter',
			'description' => Aitsu_Translate :: translate('Inserts a Twitter button'),
			'type' => 'Social Networking',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4de67494-1e80-4bd3-ad71-21df50431bca'
		);
	}
	
	public static function init($context) {

		$index = $context['index'];

		$instance = new self();
		
	    Aitsu_Content_Edit :: isBlock('Facebook', true);

		$view = $instance->_getView();

        $username = Aitsu_Content_Config_Text :: set($index, 'TwitterUsername', 'Twitter user name', 'Functional settings');
        $button = Aitsu_Content_Config_Select :: set($index, 'TwitterButton', 'Background color (light)', array('light' => 'light', 'dark' => 'dark'), 'Functional settings');
        $counter = Aitsu_Content_Config_Select :: set($index, 'TwitterCounter', 'Follower counter (true)', array('true' => true, 'false' => false), 'Functional settings');
        $textColor = Aitsu_Content_Config_Text :: set($index, 'TwitterTextColor', 'Text color (#FFFFFF)', 'Visual settings');
        $linkColor = Aitsu_Content_Config_Text :: set($index, 'TwitterLinkColor', 'Link color (#00AEFF)', 'Visual settings');
        
		if (!$username) {
			if (Aitsu_Application_Status :: isEdit()) {
		       	return '| Twitter :: ' . $index . ' |';
    		} else {
   				return '';
   			}
		}
		
		switch (Aitsu_Registry::get()->env->idlang) {
			case '1': 
				$language = 'de';
				break;
			case '2':
				$language = null; //default english
				break;
			default:
				$language = 'de';
		}
		
		$view->username = $username;
		$view->button = empty($button) || $button == 'light' ? null : $button;
        $view->counter = empty($counter) || $counter == true ? null : $counter;
        $view->textColor = empty($textColor) ? '#FFFFFF' : $textColor;
        $view->linkColor = empty($linkColor) ? '#00AEFF' : $linkColor;
        $view->language = $language;
        
        $output = '';
        if (Aitsu_Application_Status :: isEdit()) {
        	$output .= '| Twitter :: ' . $index . ' |<br/>';
        }
        
		$output .= $view->render('index.phtml');

		return $output;
	}
} 
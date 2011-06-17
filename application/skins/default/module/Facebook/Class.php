<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_Facebook_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Facebook',
			'description' => Aitsu_Translate :: translate('Inserts a Facebook plugin'),
			'type' => 'Social Networking',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4de5fbbe-51bc-4573-9857-091850431bca'
		);
	}
	
	public static function init($context) {

		$index = $context['index'];

		$instance = new self();
		
	    Aitsu_Content_Edit :: isBlock('Facebook', true);

		$view = $instance->_getView();

        $template = Aitsu_Content_Config_Select :: set($index, 'FacebookTemplate', 'Template', array('iframe' => 'iframe', 'xfbml' => 'xfbml'), 'Technical settings');
		
        $href = Aitsu_Content_Config_Link :: set($index, 'FacebookHref', 'URL to like', 'Functional settings');
        $send = Aitsu_Content_Config_Select :: set($index, 'FacebookSend', 'Send button (XFBML only)', array('true' => true, 'false' => false), 'Functional settings');
        $faces = Aitsu_Content_Config_Select :: set($index, 'FacebookFaces', 'Show faces (true)', array('true' => true, 'false' => false), 'Functional settings');
        $action = Aitsu_Content_Config_Select :: set($index, 'FacebookAction', 'Verb to display (like)', array('like' => 'like', 'recommend' => 'recommend'), 'Functional settings');
        
        $layout = Aitsu_Content_Config_Select :: set($index, 'FacebookLayout', 'Layout (standard)', array('standard' => 'standard', 'button_count' => 'button_count', 'box_count' => 'box_count'), 'Visual settings');
        $width = Aitsu_Content_Config_Text :: set($index, 'FacebookWidth', 'Width (450px)', 'Visual settings');
        $height = Aitsu_Content_Config_Text :: set($index, 'FacebookHeight', 'Height (80px)', 'Visual settings');
        $colorscheme = Aitsu_Content_Config_Select :: set($index, 'FacebookColorscheme', 'Color scheme (light)', array('light' => 'light', 'dark' => 'dark'), 'Visual settings');
        $font = Aitsu_Content_Config_Select :: set($index, 'FacebookFont', 'Font', array('default' => 'default', 'arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana'), 'Visual settings');
        
		if (!$href) {
			if (Aitsu_Application_Status :: isEdit()) {
		       	return '| Facebook :: ' . $index . ' |';
    		} else {
   				return '';
   			}
		}
		
		$heightDef = 0;
		
		switch ($layout) {
			case 'standard':
				$heightDef = !$face ? 35 : 80;
				break;
			case 'button_count':
				$heightDef = 21;
				break;
			case 'box_count':
				$heightDef = 90;
				break;
			default:
				$heightDef = 80;
		}
		
		switch (Aitsu_Registry::get()->env->idlang) {
			case '1': 
				$locale = 'de_DE';
				break;
			case '2':
				$locale = 'en_US';
				break;
			default:
				$locale = 'de_DE';
		}
		
		$view->href = empty($href) ? '/' : $href;
		$view->send = $send;
        $view->layout = !$layout ? $template == 'xfbml' ? null : 'standard' : $template;
        $view->width = !$width ? '450' : $width;
        $view->faces = !$faces ? true : $faces;
        $view->action = !$action ? 'like' : $action;
        $view->colorscheme = !$colorscheme ? 'light' : $colorscheme;
        $view->font = !$font ? null : $font;
        $view->height = !$height ? $heightDef : $height;
        $view->locale = $locale;
        
        $template = $template == 'xfbml' ? $template : 'index';
        $template = $send == true ? 'xfbml' : $template;
        
        $output = '';
        if (Aitsu_Application_Status :: isEdit()) {
        	$output .= '| Facebook :: ' . $index . ' |<br/>';
        }
        
		$output .= $view->render($template.'.phtml');

		return $output;
	}
} 
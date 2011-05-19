<?php

/**
 * @author Frank Ammari, meine experten GbR
 * @copyright Copyright &copy; 2011, meine experten GbR
 */

class Skin_Module_TagA_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'TagA',
			'description' => Aitsu_Translate :: translate('Inserts a html anchor tag'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'meine experten GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db9401b-9370-4da0-96b7-0bf150431368'
		);
	}
	
	public static function init($context) {

		$index = $context['index'];

		$instance = new self();
		
		$display = Aitsu_Content_Config_Select :: set($index, 'TagADisplay', 'Display', array('block' => 'block', 'inline' => 'inline'), 'Backend options');

		if($display == 'inline') {
		    Aitsu_Content_Edit :: isBlock('TagA', false);
		}

		$view = $instance->_getView();

        $href = Aitsu_Content_Config_Link :: set($index, 'TagAHref', 'Href', 'Link');
        $value = Aitsu_Content_Config_Text :: set($index, 'TagAValue', 'Value', 'Link');
        
        $title = Aitsu_Content_Config_Text :: set($index, 'TagATitle', 'title', 'Additional link attributes');
        $target = Aitsu_Content_Config_Select :: set($index, 'TagATarget', 'target', array('none' => 'none', '_blank' => '_blank', '_self' => '_self', '_top' => '_top','_parent' => '_parent'), 'Additional link attributes');
        $id = Aitsu_Content_Config_Text :: set($index, 'TagAId', 'id', 'Additional link attributes');
        $class = Aitsu_Content_Config_Text :: set($index, 'TagAClass', 'class', 'Additional link attributes');
        $style = Aitsu_Content_Config_Text :: set($index, 'TagAStyle', 'style', 'Additional link attributes');

		if (!$href || !$value) {
			if (Aitsu_Registry :: isEdit()) {
		       	return '// TagA ' . $index . ' //';
    		} else {
   				return '';
   			}
		}
		
		$view->href = empty($href) ? '/' : $href;
		$view->value = $value;

		$view->title = empty($title) ?  NULL : ' title="'. $title .'"';
		$view->target = empty($target) || $target == 'none' ? NULL : ' target="'. $target .'"';

		$view->id = empty($id) ? NULL : ' id="' . $id . '"';
		$view->class = empty($class) ? NULL : ' class="' . $class . '"';
		$view->style = empty($style) ? NULL : ' style="' . $style . '"';

		$output = $view->render('index.phtml');

		return $output;
	}
}
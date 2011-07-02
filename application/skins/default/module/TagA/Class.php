<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_TagA_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$display = Aitsu_Content_Config_Select :: set($this->_index, 'TagADisplay', 'Display', array('block' => 'block', 'inline' => 'inline'), 'Backend options');

		if($display == 'inline') {
		    Aitsu_Content_Edit :: isBlock('TagA', false);
		}

		$view = $this->_getView();

        $href = Aitsu_Content_Config_Link :: set($this->_index, 'TagAHref', 'Href', 'Link');
        $value = Aitsu_Content_Config_Text :: set($this->_index, 'TagAValue', 'Value', 'Link');
        
        $title = Aitsu_Content_Config_Text :: set($this->_index, 'TagATitle', 'title', 'Additional link attributes');
        $target = Aitsu_Content_Config_Select :: set($this->_index, 'TagATarget', 'target', array('none' => 'none', '_blank' => '_blank', '_self' => '_self', '_top' => '_top','_parent' => '_parent'), 'Additional link attributes');
        $id = Aitsu_Content_Config_Text :: set($this->_index, 'TagAId', 'id', 'Additional link attributes');
        $class = Aitsu_Content_Config_Text :: set($this->_index, 'TagAClass', 'class', 'Additional link attributes');
        $style = Aitsu_Content_Config_Text :: set($this->_index, 'TagAStyle', 'style', 'Additional link attributes');

		if (!$href || !$value) {
			if (Aitsu_Application_Status :: isEdit()) {
		       	return '| TagA :: ' . $this->_index . ' |';
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
<?php


/**
 * This module lets you integrate output of a module of another article. The
 * inner content of the output remains editable. So you are able not only to include
 * the output, but also to maintain the content from different places.
 * 
 * The module is somehow experimental. You should use it only, if you are able
 * to extend it to your needs if necessary.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Content_Tunnel_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$placeHolder = Aitsu_Translate :: _('Foreign content placeholder text');

		$sourceCat = (preg_match('/_(\\d*)$/', $this->_index, $match) ? $match[1] : 0);

		$idart = Aitsu_Ee_Config_ArticlesBySpecifiedCategoryAsSelect :: set($this->_index, 'moduloutput_idart', 'Idart', array (
			$sourceCat
		), Aitsu_Registry :: get()->env->idlang);

		$method = Aitsu_Ee_Config_Select :: set($this->_index, 'moduloutput_method', '', array (
			'Spaltenblock 1' => 'Template:Spaltenblock1',
			'Spaltenblock 2' => 'Template:Spaltenblock2'
		), 'Container');

		$moduleName = strtok($method, ':');
		$moduleIndex = strtok("\n");

		$client = Aitsu_Registry :: get()->env->idclient;

		if ($idart == null) {
			$output = '<div>' . $placeHolder . '</div>';
		} else {
			Aitsu_Content_Edit :: register(false);
			$output = Aitsu_Core_Module :: factory($idart, null, null, $moduleName)->getOutput(true, Aitsu_Registry :: get()->env->edit, $moduleIndex);
			Aitsu_Content_Edit :: register();

			if (empty ($output)) {
				$output = $placeHolder;
			}
		}

		if (Aitsu_Registry :: isEdit()) {
			return '<div style="border:1px dashed #EC7537; padding:5px;"><div style="height:20px; background-color:#EC7537; font-weight:bold; color:white; padding-top:7px; padding-left:5px;">Output of ' . $method . ' in article ' . $idart . '</div>' . $output . '</div>';
		}

		return $output;
	}
}
?>

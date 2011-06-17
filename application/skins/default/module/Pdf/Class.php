<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 *
 */

class Skin_Module_Pdf_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Pdf',
			'description' => Aitsu_Translate :: translate('Returns PDF files'),
			'type' => 'Files',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4de4db14-c150-4342-bb48-683250431bca'
		);
	}
	
	public static function init($context) {

		$index = $context['index'];
		
		$files = Aitsu_Content_Config_Media :: set($index, 'PdfFile', 'Pdf file', 'files');
		if(!empty($files) && !is_null($files)) {
			$files = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $files);
		} else {
			$files = Aitsu_Persistence_View_Media :: byFileExtension(Aitsu_Registry :: get()->env->idart, 'pdf');
		}

		if (!$files) {
			if (Aitsu_Application_Status :: isEdit()) {
				return '| File :: ' . $index . ' |';
			} else {
				return '';
			}
		}
		
		$instance = new self();
		$view = $instance->_getView();

		$view->files = $files;

		$output = $view->render('index.phtml');

		return $output;
	}
}
?>

<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ImageController.php 19166 2010-10-05 16:38:00Z akm $}
 */

class ImageController extends Zend_Controller_Action {

	public function init() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	}

	public function mediaAction() {

		$file = Aitsu_Db :: fetchRow('' .
		'select * from _media where mediaid = :mediaid', array (
			':mediaid' => $this->getRequest()->getParam('id')
		));
		
		$idart = $file['idart'];
		$filename = $file['filename'];
		$width = $this->getRequest()->getParam('width');
		$height = $this->getRequest()->getParam('height');
		$boxed = $this->getRequest()->getParam('boxed');

		$imageSrc = Aitsu_Core_Image_Source :: getInstance();
		$imageSrc->setThumbUrl("$width/$height/$boxed/$idart/$filename");

		Aitsu_Core_Image_Resize :: getInstance()->setImageSource($imageSrc)->outputImage();

		exit ();
	}

}
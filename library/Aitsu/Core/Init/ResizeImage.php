<?php


/**
 * Image resizer.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ResizeImage.php 15550 2010-03-24 14:29:11Z akm $}
 */

class Aitsu_Core_Init_ResizeImage implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (!isset($_GET['imageurl'])) {
			return;
		}
return;
		$imageSrc = Aitsu_Core_Image_Source :: getInstance();
		$imageSrc->setThumbUrl($_GET['imageurl']);

		Aitsu_Core_Image_Resize :: getInstance()->setImageSource($imageSrc)->outputImage();

		exit (0);
	}
}
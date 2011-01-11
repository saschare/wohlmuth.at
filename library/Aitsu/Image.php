<?php


/**
 * Image resizing and caching.
 * The class offers four different types of resizing (type 0 to 3) using
 * the original image dimension, the specified width and height and the
 * specified area of interest (AOI).
 * 
 * Type 0: The image is resized while the original width/height ratio is 
 * maintained. The image will be downsized if necessary, but not enlarged.
 * If the original image is smaller than the specified rectangle, the original
 * dimenision is used.
 * 
 * Type 1: The image is scaled to the specified width and height using the
 * maximum possible area of the original image having the specified width/height
 * ratio. The area is taken from the image's center.
 * 
 * Type 2: The image is scaled to the specified width and height using the
 * maximum possible area of interest (specified in the meta data of the image)
 * without enlarging the original image. If the target dimension is greater
 * than the area of interest, the area is extended.
 * 
 * Type 3: The image is scaled to the specified with and height using the
 * maximum possible area of the image and using the center of the area of 
 * interest as the center of the target image.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Image {
	
	protected $_thumbDir;
	protected $_imagePath;
	protected $_pathInfo;
	
	protected $_type = 0;
	protected $_width = 100;
	protected $_height = 100;

	protected function __construct() {

		$this->_thumbDir = APPLICATION_PATH . '/data/thumbs/';
	}

	public static function getInstance($imagePath = null, $width = null, $height = null, $type = null) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}
		
		if ($imagePath != null) {
			$instance->_imagePath = APPLICATION_PATH . '/data/media/' . $imagePath;
		}

		if ($width != null) {
			$instance->_width = $width;
		}

		if ($height != null) {
			$instance->_height = $height;
		}

		if ($type != null) {
			$instance->_type = $type;
		}

		return $instance;
	}

	protected function _sendHeaders() {

		$expires = 60 * 60 * 24 * 7;
		header('Pragma: public');
		header('Cache-Control: max-age=' . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
		header('Content-Disposition: inline');
		header('Content-type: image/' . $this->imageSrc->getImageType());
	}
	
	public function outputImage() {

		$this->_setDimToAllowedValues();

		$imagename = $this->_resize();

		if ($imagename == FALSE) {
			header("HTTP/1.1 404 Not Found");
			exit;
		}

		$this->_sendHeaders();

		readfile($imagename);
	}
	
	protected function _resize() {

		if (!file_exists($this->_imagePath)) {
			if (preg_match('@([^/]*)/([^/]*)\\.(.{1,5})$@', $this->_imagePath, $match)) {
				$mediaId = Aitsu_Db :: fetchOne('' .
				'select mediaid from _media ' .
				'where ' .
				'	idart = :idart ' .
				'	and filename = :filename ' .
				'	and deleted is null ' .
				'order by ' .
				'	mediaid desc ' .
				'limit 0, 1 ', array (
					':idart' => $match[1],
					':filename' => $match[2] . '.' . $match[3]
				));
				if ($mediaId) {
					$this->_imagePath = $match[1] . '/' . $mediaId . '.' . strtolower($match[3]);
				}
			}
		}

		if (!file_exists($this->_imagePath)) {
			/*
			 * Abort if the original file does not exist.
			 */
			return false;
		}

		$targetDir = preg_replace('/\\.[a-zA-Z]{3,4}$/', '', $this->imagePath);
		if (!is_dir($this->thumbDir . $targetDir)) {
			mkdir($this->thumbDir . $targetDir, 0777, true);
		}
		
		$this->_targetPath = $targetDir . '/' . $this->_width . '.' . $this->_height . $this->_type . "." . $this->_getPathInfo('extension');

		if (file_exists($this->thumbDir . $this->_targetPath)) {
			/*
			 * Scaled image already exists and will be returned.
			 */
			return $this->thumbDir . $this->_targetPath;
		}

		return $this->_resizeGd();
	}
	
	protected function _getPathInfo($type) {
		
		$this->_pathInfo = pathinfo($this->_imagePath);
		
		if ($type == 'extension' && isset($this->_pathInfo['extension']) && $this->_pathInfo['extension'] == 'jpg') {
			return 'jpeg';
		}
		
		if (isset($this->_pathInfo[$type])) {
			return $this->_pathInfo[$type];
		}
		
		return null;
	}
	
	protected function _resizeGd() {

		$src_im = null;

		$imageSourceDimensions = getimagesize($this->_imagePath);

		if ($imageSourceDimensions[0] <= $this->_width && $imageSourceDimensions[1] <= $this->_height && $this->_type == 0) {
			return $this->_imagePath;
		}

		/*
		 * Calculate the target dimension of the image.
		 */
		$targetSize = $this->_getTargetSize($imageSourceDimensions[0], $imageSourceDimensions[1]);
		$targetWidth = $targetSize[0];
		$targetHeight = $targetSize[1];

		if ($this->imageSrc->isBox() || $this->imageSrc->isEBox()) {
			/*
			 * Only a certain area has to be used, taken from the center
			 * of the source image.
			 */
			if ($imageSourceDimensions[0] / $imageSourceDimensions[1] < $targetWidth / $targetHeight) {
				$width = $imageSourceDimensions[0];
				$height = $imageSourceDimensions[0] * $targetHeight / $targetWidth;
				$x = 0;
				$y = ($imageSourceDimensions[1] - $height) / 2;
			} else {
				$width = $imageSourceDimensions[1] * $targetWidth / $targetHeight;
				$height = $imageSourceDimensions[1];
				$x = ($imageSourceDimensions[0] - $width) / 2;
				$y = 0;
			}
			$width = round($width);
			$height = round($height);
			$x = round($x);
			$y = round($y);

			if ($this->imageSrc->isEBox()) {
				/*
				 * A certain area around the given area of interest has
				 * to be used. If no information about the area of interest
				 * is available, the center of the source image is used instead.
				 * 
				 * We just have to change the x and y coordinate values to make
				 * sure the area of interest is a near as possible in the center
				 * of the target image.
				 */
				$dimenpos = $this->_calculateDimensionAndOffset($imageSource, $width, $height, $targetWidth, $targetHeight, $imageSourceDimensions[0], $imageSourceDimensions[1]);
				$width = $dimenpos['width'];
				$height = $dimenpos['height'];
				$x = $dimenpos['x'];
				$y = $dimenpos['y'];
			}
		} else {
			/*
			 * Full size image is used.
			 */
			$width = $imageSourceDimensions[0];
			$height = $imageSourceDimensions[1];
			$x = 0;
			$y = 0;
		}

		$dst_im = imagecreatetruecolor($targetWidth, $targetHeight);
		$weiss = ImageColorAllocate($dst_im, 255, 255, 255);
		imagefill($dst_im, 0, 0, $weiss);

		if ($src_im == null) {
			$src_im = $this->_getGdSrcImage($imageSourceDimensions[2], $imageSource);
		}

		imagecopyresampled($dst_im, $src_im, 0, 0, $x, $y, $targetWidth, $targetHeight, $width, $height);
		imagedestroy($src_im);
		imagejpeg($dst_im, $this->thumbDir . $targetName, 100);
		imagedestroy($dst_im);

		return $this->thumbDir . $targetName;
	}
}
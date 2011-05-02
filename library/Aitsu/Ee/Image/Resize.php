<?php


/**
 * Image resizer.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Resize.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Ee_Image_Resize {

	private $basePath;
	private $thumbsPath;
	private $imagePath;
	private $imageSrc = NULL;
	private $useImageMagick = FALSE;

	private function __construct() {

		$this->basePath = Aitsu_Registry :: get()->config->dir->image->basePath;
		$this->thumbDir = Aitsu_Registry :: get()->config->dir->image->thumbs;
	}
	
	public static function getInstance() {
		
		static $instance;
		
		if (!isset($instance)) {
			$instance = new self();
		}
		
		return $instance;
	}
	
	public function setImageSource(Aitsu_Ee_Image_Source $image) {
		$this->imageSrc = $image;
		
		return $this;
	}

	private function sendHeaders() {

		$expires = 60 * 60 * 24 * 7;
		header('Pragma: public');
		header('Cache-Control: max-age=' . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
		header('Content-Disposition: inline');
		header('Content-type: image/' . $this->imageSrc->getImageType());
	}

	public function outputImage($outputPath = NULL, $imageSource = null) {
		
		if ($outputPath != NULL) {

			$this->thumbDir .= "/$outputPath/";
			$this->thumbDir = preg_replace('@\/{2}@', '/', $this->thumbDir);
			
			if (!is_dir($this->thumbDir)) {
				mkdir($this->thumbDir);
			}
		}

		$imagename = $this->resize();

		if ($imagename == FALSE) {
			header("HTTP/1.1 404 Not Found");
			exit;
		}

		$this->sendHeaders();
		readfile($imagename);
	}
	
	private function resize() {
		
		if ($this->imageSrc == NULL || $this->imageSrc->getThumbWidth() == 0 || $this->imageSrc->getThumbHeight() == 0) {
			return FALSE;
		}		
		
		$this->imagePath = $this->imageSrc->getImagePath();
		
		$imageSource = $this->basePath . $this->imagePath;

		if (!file_exists($imageSource) && !$this->imageSrc->isImage()) {
			/*
			 * Wenn die Originaldatei nicht vorhanden ist, ist abzubrechen.
			 */
			return FALSE;
		}	
		
		$box = ($this->imageSrc->isBox()) ? ('.b') : ('');
		$targetName = str_replace('/', '_', preg_replace('@\\.\\w*$@', '', $this->imagePath)) . '.' . $this->imageSrc->getThumbWidth() . 'x' . $this->imageSrc->getThumbHeight() . $box . "." . $this->imageSrc->getImageType();
		if ($this->imageSrc->getWatermark() !== FALSE) {
			$targetName = 'w.' . $targetName;
		}

		if (file_exists($this->thumbDir . $targetName)) {
			/*
			 * Skaliertes Bild liegt bereits vor und wird zurückgegeben.
			 */
			return $this->thumbDir . $targetName;
		}
		
		if ($this->imageSrc->isImage()) {
			return $this->resizeGd(null, $targetName);
		}

		return $this->resizeGd($imageSource, $targetName);
	}

	private function resizeGd($imageSource, $targetName) {

		$src_im = null;
		
		$width = $this->imageSrc->getThumbWidth();
		$height = $this->imageSrc->getThumbHeight();
		$box = ($this->imageSrc->isBox()) ? ('.b') : ('');

		if ($this->imageSrc->isImage()) {
			$src_im = imagecreatefromstring($this->imageSrc->getImage());
			$imageSourceDimensions[0] = imagesx($src_im);
			$imageSourceDimensions[1] = imagesy($src_im);
		} else {
			$imageSourceDimensions = getimagesize($imageSource);
		}

		if ($imageSourceDimensions[0] <= $width && $imageSourceDimensions[1] <= $height && !$this->imageSrc->isBox()) {
			/*
			 * Das Bild muss (kann) nicht skaliert werden.
			 */
			if ($this->imageSrc->getWatermark() != FALSE || $src_im != null) {
				
				$dst_im = imagecreatetruecolor($imageSourceDimensions[0], $imageSourceDimensions[1]);		
				$weiss = ImageColorAllocate($dst_im, 255, 255, 255);
				imagefill($dst_im, 0, 0, $weiss);
		
				if ($src_im == null) {
					$src_im = $this->getGdSrcImage($imageSourceDimensions[2], $imageSource);
				}
						
				imagecopy($dst_im, $src_im, 0, 0, 0, 0, $imageSourceDimensions[0], $imageSourceDimensions[1]);						
				
				switch ($this->imageSrc->getImageType()) {
					case 'jpg':
						imagejpeg($dst_im, $this->thumbDir . $targetName);			
						break;
						
					case 'png':
						imagepng($dst_im, $this->thumbDir . $targetName);			
						break;	
						
					case 'gif':
						imagegif($dst_im, $this->thumbDir . $targetName);			
						break;										
				}	
					
				imagedestroy($dst_im);
				
				return $this->thumbDir . $targetName;
			}
			
			return $this->basePath . $this->imagePath;
		}

		/*
		 * Zielgrösse des Bildes ermitteln.
		 */
		$targetSize = $this->getTargetSize($imageSourceDimensions[0], $imageSourceDimensions[1]);
		$targetWidth = $targetSize[0];
		$targetHeight = $targetSize[1];

		if ($this->imageSrc->isBox()) {
			/*
			 * Vom Quellbild wird nur ein Ausschnitt verwendet werden.
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
			
		} else {
			/*
			 * Bild wird vollständig verwendet.
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
			$src_im = $this->getGdSrcImage($imageSourceDimensions[2], $imageSource);
		}
				
		imagecopyresampled($dst_im, $src_im, 0, 0, $x, $y, $targetWidth, $targetHeight, $width, $height);		
		
		imagedestroy($src_im);
		
		imagejpeg($dst_im, $this->thumbDir . $targetName, 100);		
		
		imagedestroy($dst_im);

		return $this->thumbDir . $targetName;
	}
		
	/*
	 * Ermittelt die Zielgrösse des Bildes
	 */	
	private function getTargetSize($srcWidth, $srcHeight) {
		
		$width  = $this->imageSrc->getThumbWidth();
		$height = $this->imageSrc->getThumbHeight();
				
		/*
		 * Zielgrösse des Bildes ermitteln.
		 */
		if ($this->imageSrc->isBox()) {
			$targetWidth = $width;
			$targetHeight = $height;
		} else {
			$slopeSource = $srcHeight / $srcWidth;
			$slopeTarget = $height / $width;
			if ($slopeSource < $slopeTarget) {
				/*
				 * Auf Breite skalieren
				 */
				$targetWidth = (int) $width;
				$targetHeight = ($targetWidth / $srcWidth) * $srcHeight;
			} else {
				/*
				 * Auf Höhe skalieren
				 */
				$targetHeight = (int) $height;
				$targetWidth = ($targetHeight / $srcHeight) * $srcWidth;
			}
		}
		
		$targetSize = array();
		$targetSize[0] = (int) round($targetWidth);
		$targetSize[1] = (int) round($targetHeight);
				
		return $targetSize;
	}
	
	private function getGdSrcImage($type, $imageSource) {

		if ($type == IMG_GIF) {
			return imagecreatefromGIF($imageSource);
		}
		
		if ($type == IMG_JPEG || $type == IMG_JPG) {
			return ImageCreateFromJPEG($imageSource);
		}
		
		if ($type == IMG_PNG || $type == 3) {
			return ImageCreateFromPNG($imageSource);
		} 
		
		return imagecreatefromgd($imageSource);
	}
	
	
	private function addGdWatermarkToImage(& $dst_im) {

		$watermarkfile = imagecreatefrompng($this->imageSrc->getWatermark());
		
		// Hintergrund transparent setzen 
		$transp = imagecolorallocate($watermarkfile, 0, 0, 0);
		imagecolortransparent($watermarkfile, $transp);
		$transp = imagecolorallocate($watermarkfile, 255, 255, 255);
		imagecolortransparent($watermarkfile, $transp);		
				
		$waternarkpic_width  = imagesx($watermarkfile);
		$waternarkpic_height = imagesy($watermarkfile);
		$watermarkdest_x     = imagesx($dst_im) / 2 - ($waternarkpic_width / 2);
		$watermarkdest_y     = imagesy($dst_im) / 2 - ($waternarkpic_height / 2);

		imagecopy($dst_im, $watermarkfile, $watermarkdest_x, $watermarkdest_y, 0, 0, $waternarkpic_width, $waternarkpic_height);
		imagedestroy($watermarkfile);
	}
	
	public function imageExists() {
		
		$box = ($this->imageSrc->isBox()) ? ('.b') : ('');
		$targetName = str_replace('/', '_', preg_replace('@\\.\\w*$@', '', $this->imageSrc->getImagePath())) . '.' . $this->imageSrc->getThumbWidth() . 'x' . $this->imageSrc->getThumbHeight() . $box . "." . $this->imageSrc->getImageType();

		if (file_exists($this->thumbDir . $targetName)) {
			return true;
		}
		
		return false;
	}
}
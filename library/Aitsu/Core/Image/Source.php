<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2009, w3concepts AG
 */

class Aitsu_Core_Image_Source {

	private $basePath;
	private $thumbsPath;
	private $thumbWidth = NULL, $thumbHeight = NULL;
	private $imagePath;
	private $box = FALSE;
	private $name = NULL;
	private $width, $height;
	private $imageType = NULL;
	private $supportedTypes = array('jpg', 'jpeg', 'png', 'gif');
	private $image = null;
	
	private function __construct($thumbWidth, $thumbHeight) {

		$this->basePath = isset(Aitsu_Registry :: get()->config->dir->image->basePath) ? Aitsu_Registry :: get()->config->dir->image->basePath : '';
		$this->thumbDir = isset(Aitsu_Registry :: get()->config->dir->image->thumbs) ? Aitsu_Registry :: get()->config->dir->image->thumbs : '';
		
		$this->thumbWidth  = $thumbWidth;
		$this->thumbHeight = $thumbHeight;
	}
	
	public static function getInstance($thumbWidth = NULL, $thumbHeight = NULL) {
		
		static $instance;
		
		if (!isset($instance)) {
			$instance = new self($thumbWidth, $thumbHeight);
		}
		
		return $instance;
	}
	
	public function getThumbUrl($baseUrl = '') {
		
		if ($this->thumbWidth == NULL || $this->thumbHeight == NULL) {
			throw new Exception('Thumb dimensions not set');
		}
		
		return $baseUrl . "image/{$this->thumbWidth}/{$this->thumbHeight}/{$this->box}/{$this->imagePath}";
	}
	
	public function setThumbUrl($url) {
		
		preg_match_all('@(\\d*)/(\\d*)/(\\d)/(.*)\\.(\\w*$)@', $url, $matches);

		$this->thumbWidth  = current($matches[1]);
		$this->thumbHeight = current($matches[2]);
		$this->box         = current($matches[3]);
		$this->imagePath   = current($matches[4]) . "." . current($matches[5]);
		$this->imageType   = strtolower(current($matches[5]));
		
		if (!in_array($this->imageType, $this->supportedTypes)) {
			$path_parts = pathinfo(current($matches[4]));
			$this->imagePath = (isset($path_parts['dirname']) ? $path_parts['dirname'] : '') . '/__' . $path_parts['filename'] . '.jpg';
			$this->imageType = 'jpeg';
		}
	}
	
	public function isBox() {
		return $this->box == 1;
	}
	
	public function isEBox() {
		return $this->box == 2;
	}
	
	public function setBox($box) {
		$this->box = $box;
	}
	
	public function getThumbWidth() {
		return $this->thumbWidth;
	}
	
	public function setThumbWidth($width) {
		$this->thumbWidth = $width;
	}
	
	public function getThumbHeight() {
		return $this->thumbHeight;
	}
	
	public function setThumbHeight($height) {
		$this->thumbHeight = $height;
	}	
	
	public function getImagePath() {
		return $this->imagePath;
	}
	
	public function setImagePath($path) {
		$this->imagePath = $path;
	}		
	
	public function getImageType() {
		
		if ($this->imageType == 'jpg') {
			return 'jpeg';
		}
		
		return $this->imageType;
	}
	
	public function setImageType($type) {
		$this->imageType = $type;
	}
	
	public function setImage($image) {
		$this->image = $image;
	}
	
	public function getImage() {
		return $this->image;
	}
	
	public function isImage() {
		return $this->image != null;
	}
}	
?>

<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2009, w3concepts AG
 */
class Aitsu_Core_Image_Resize {

    protected $basePath;
    protected $thumbsPath;
    protected $imagePath;
    protected $imageSrc = NULL;

    protected function __construct() {

        $this->basePath = isset(Aitsu_Registry :: get()->config->dir->image->basePath) ? Aitsu_Registry :: get()->config->dir->image->basePath : '';

        $thumbDir = APPLICATION_PATH . '/data/thumbs/';
        if (!file_exists($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }
        $this->thumbDir = $thumbDir . '/';
    }

    public static function getInstance() {

        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    public function setImageSource(Aitsu_Core_Image_Source $image) {
        $this->imageSrc = $image;

        return $this;
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

    public function outputImage($outputPath = NULL, $imageSource = null) {

        $this->_setDimToAllowedValues();

        $imagename = $this->_resize();

        if ($imagename == FALSE) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        $this->_sendHeaders();
        
        $this->_makeAvailableTransparent($imagename);

        readfile($imagename);
    }
    
    protected function _makeAvailableTransparent($imagename) {
    	
    	$pathInfo = pathinfo($_GET['imageurl']);
    	
    	$dir = APPLICATION_PATH . '/data/cachetransparent/image/' . $pathInfo['dirname'];
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        copy($imagename, $dir . '/' . $pathInfo['basename']);
    }

    protected function _resize() {

        if ($this->imageSrc == NULL || $this->imageSrc->getThumbWidth() == 0 || $this->imageSrc->getThumbHeight() == 0) {
            return FALSE;
        }

        $this->imagePath = $this->imageSrc->getImagePath();

        $imageSource = APPLICATION_PATH . '/data/media/' . $this->imagePath;

        if (!file_exists($imageSource)) {
            if (preg_match('@([^/]*)/([^/]*)\\.(.{1,5})$@', $this->imagePath, $match)) {

                if (empty($match[1]) || $match[1] == null || $match[1] == 0) {
                $mediaId = Aitsu_Db :: fetchOneC(60 * 60, '' .
                                'select mediaid from _media ' .
                                'where ' .
                                '	idart is null ' .
                                '	and filename = :filename ' .
                                '	and deleted is null ' .
                                'order by ' .
                                '	mediaid desc ' .
                                'limit 0, 1 ', array(
                            ':filename' => $match[2] . '.' . $match[3]
                        ));
                } else {
                    $mediaId = Aitsu_Db :: fetchOneC(60 * 60, '' .
                                    'select mediaid from _media ' .
                                    'where ' .
                                    '	idart = :idart ' .
                                    '	and filename = :filename ' .
                                    '	and deleted is null ' .
                                    'order by ' .
                                    '	mediaid desc ' .
                                    'limit 0, 1 ', array(
                                ':idart' => $match[1],
                                ':filename' => $match[2] . '.' . $match[3]
                            ));
                }

                if ($mediaId) {
                    $this->imagePath = $match[1] . '/' . $mediaId . '.' . strtolower($match[3]);
                    $imageSource = APPLICATION_PATH . '/data/media/' . $this->imagePath;
                }
            }

            if (!file_exists($imageSource)) {
                $imageSource = $this->basePath . '/' . $this->imagePath;
            }
        }

        if (!file_exists($imageSource) && !$this->imageSrc->isImage()) {
            /*
             * Abort if the original file does not exist.
             */
            return false;
        }

        $box = ($this->imageSrc->isBox()) ? ('.b') : ('');
        $box = ($this->imageSrc->isEBox()) ? ('.e') : $box;

        $targetDir = preg_replace('/\\.[a-zA-Z]{3,4}$/', '', $this->imagePath);
        if (!is_dir($this->thumbDir . $targetDir)) {
            mkdir($this->thumbDir . $targetDir, 0777, true);
        }

        $targetName = $targetDir . '/' . $this->imageSrc->getThumbWidth() . '.' . $this->imageSrc->getThumbHeight() . $box . "." . $this->imageSrc->getImageType();

        if (file_exists($this->thumbDir . $targetName)) {
            /*
             * Scaled image already exists and will be returned.
             */
            return $this->thumbDir . $targetName;
        }

        if ($this->imageSrc->isImage()) {
            return $this->_resizeGd(null, $targetName);
        }

        return $this->_resizeGd($imageSource, $targetName);
    }

    protected function _resizeGd($imageSource, $targetName) {

        $src_im = null;

        $width = $this->imageSrc->getThumbWidth();
        $height = $this->imageSrc->getThumbHeight();
        $box = ($this->imageSrc->isBox()) ? ('.b') : ('');
        $box = ($this->imageSrc->isEBox()) ? ('.e') : $box;

        if ($this->imageSrc->isImage()) {
            $src_im = imagecreatefromstring($this->imageSrc->getImage());
            $imageSourceDimensions[0] = imagesx($src_im);
            $imageSourceDimensions[1] = imagesy($src_im);
        } else {
            $imageSourceDimensions = getimagesize($imageSource);
        }

        if ($imageSourceDimensions[0] <= $width && $imageSourceDimensions[1] <= $height && !$this->imageSrc->isBox()) {

            /*
             * The image does not need to be scaled.
             */
            if ($src_im != null) {

                $dst_im = imagecreatetruecolor($imageSourceDimensions[0], $imageSourceDimensions[1]);
                $white = ImageColorAllocate($dst_im, 255, 255, 255);
                imagefill($dst_im, 0, 0, $white);

                if ($src_im == null) {
                    $src_im = $this->_getGdSrcImage($imageSourceDimensions[2], $imageSource);
                }

                imagecopy($dst_im, $src_im, 0, 0, 0, 0, $imageSourceDimensions[0], $imageSourceDimensions[1]);

                switch ($this->imageSrc->getImageType()) {
                    case 'png' :
                        imagepng($dst_im, $this->thumbDir . $targetName);
                        break;
                    case 'gif' :
                        imagegif($dst_im, $this->thumbDir . $targetName);
                        break;
                    default :
                        imagejpeg($dst_im, $this->thumbDir . $targetName);
                        break;
                }

                imagedestroy($dst_im);

                return $this->thumbDir . $targetName;
            }

            return $imageSource;
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
        $white = ImageColorAllocate($dst_im, 255, 255, 255);
        imagefill($dst_im, 0, 0, $white);

        imageSaveAlpha($dst_im, true);
        ImageAlphaBlending($dst_im, false);

        if ($src_im == null) {
            $src_im = $this->_getGdSrcImage($imageSourceDimensions[2], $imageSource);
        }

        imageSaveAlpha($src_im, true);
        ImageAlphaBlending($src_im, false);

        imagecopyresampled($dst_im, $src_im, 0, 0, $x, $y, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($src_im);

        $quality = isset(Aitsu_Registry :: get()->config->image->quality) ? Aitsu_Registry :: get()->config->image->quality : 75;

        switch ($this->imageSrc->getImageType()) {
            case 'png' :
                imagepng($dst_im, $this->thumbDir . $targetName, 0);
                break;
            case 'gif' :
                imagegif($dst_im, $this->thumbDir . $targetName);
                break;
            default :
                imagejpeg($dst_im, $this->thumbDir . $targetName, $quality);
                break;
        }

        imagedestroy($dst_im);

        return $this->thumbDir . $targetName;
    }

    /**
     * Calculates and returns the target dimension of the image.
     * @param Integer Width of the source image.
     * @param Integer Height of the source image.
     * @return Array Target width with the index 0 and target height with the index 1.
     */
    protected function _getTargetSize($srcWidth, $srcHeight) {

        $width = $this->imageSrc->getThumbWidth();
        $height = $this->imageSrc->getThumbHeight();

        /*
         * Calculate target dimension of the image.
         */
        if ($this->imageSrc->isBox() || $this->imageSrc->isEBox()) {
            $targetWidth = $width;
            $targetHeight = $height;
        } else {
            $slopeSource = $srcHeight / $srcWidth;
            $slopeTarget = $height / $width;
            if ($slopeSource < $slopeTarget) {
                /*
                 * Scale based on width of the source image.
                 */
                $targetWidth = (int) $width;
                $targetHeight = ($targetWidth / $srcWidth) * $srcHeight;
            } else {
                /*
                 * Scale based on the height of the source image.
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

    protected function _getGdSrcImage($type, $imageSource) {

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

    public function imageExists() {

        $box = ($this->imageSrc->isBox()) ? ('.b') : ('');
        $box = ($this->imageSrc->isEBox()) ? ('.e') : $box;

        $targetName = str_replace('/', '_', preg_replace('@\\.\\w*$@', '', $this->imageSrc->getImagePath())) . '.' . $this->imageSrc->getThumbWidth() . 'x' . $this->imageSrc->getThumbHeight() . $box . "." . $this->imageSrc->getImageType();

        if (file_exists($this->thumbDir . $targetName)) {
            return true;
        }

        return false;
    }

    protected function _setDimToAllowedValues() {

        if (isset(Aitsu_Registry :: get()->config->image->allowed->widths)) {
            $widths = explode(',', Aitsu_Registry :: get()->config->image->allowed->widths);
            if (!in_array($this->imageSrc->getThumbWidth(), $widths)) {
                rsort($widths, SORT_NUMERIC);
                $this->imageSrc->setThumbWidth($widths[0]);
                foreach ($widths as $width) {
                    if ($width <= $this->imageSrc->getThumbWidth()) {
                        $this->imageSrc->setThumbWidth($width);
                        break;
                    }
                }
            }
        }

        if (isset(Aitsu_Registry :: get()->config->image->allowed->heights)) {
            $heights = explode(',', Aitsu_Registry :: get()->config->image->allowed->heights);
            if (!in_array($this->imageSrc->getThumbHeight(), $heights)) {
                rsort($heights, SORT_NUMERIC);
                $this->imageSrc->setThumbHeight($heights[0]);
                foreach ($heights as $height) {
                    if ($height <= $this->imageSrc->getThumbHeight()) {
                        $this->imageSrc->setThumbHeight($height);
                        return;
                    }
                }
            }
        }
    }

    protected function _calculateDimensionAndOffset($imageSource, $width, $height, $tWidth, $tHeight, $oWidth, $oHeight) {

        try {
            preg_match('/(\\d*)\\.[a-zA-Z]{3,4}$/', $imageSource, $match);
            $dims = Aitsu_Db :: fetchRow('' .
                            'select xtl, ytl, xbr, ybr from _media ' .
                            'where mediaid = :mediaid', array(
                        ':mediaid' => $match[1]
                    ));
            $xlt = $dims['xtl'];
            $ylt = $dims['ytl'];
            $xbr = $dims['xbr'];
            $ybr = $dims['ybr'];
        } catch (Exception $e) {
            $xlt = 0;
            $ylt = 0;
            $xbr = 1;
            $ybr = 1;
        }

        /*
         * Dimension of the area of interest.
         */
        $aiWidth = $oWidth * ($xbr - $xlt);
        $aiHeight = $oHeight * ($ybr - $ylt);

        /*
         * We reduce the width and height to a dimension that is at least
         * as great as the target dimension, at least as great as the area
         * of interest and as small as possible.
         */
        $maxWidthFactor = $oWidth / max($tWidth, $aiWidth);
        $maxHeightFactor = $oHeight / max($tHeight, $aiHeight);

        $scalingFactor = min($maxWidthFactor, $maxHeightFactor);

        $width = round($width / $scalingFactor);
        $height = round($height / $scalingFactor);

        /*
         * Then we have to calculate the offset of the area from the upper
         * left corner (0,0) of the image. For that reason we try to put the
         * center of source area to the center of the ROI (region of interest).
         * If the source area would cross the image borders, we move it east, west,
         * south or north, whatever is appropriate.
         */
        $xA = round($width / 2);
        $yA = round($height / 2);
        $xRoi = round($oWidth * ($xlt + $xbr) / 2);
        $yRoi = round($oHeight * ($ylt + $ybr) / 2);

        $x = $xRoi - $xA;
        $y = $yRoi - $yA;

        $x = max(0, $x);
        $y = max(0, $y);
        $x = min($x, $oWidth - $width);
        $y = min($y, $oHeight - $height);

        return array(
            'width' => $width,
            'height' => $height,
            'x' => $x,
            'y' => $y
        );
    }

}
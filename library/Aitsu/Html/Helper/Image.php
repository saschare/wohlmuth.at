<?php


/**
 * Image HTML helper.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Image.php 15678 2010-03-30 17:52:31Z akm $}
 */

class Aitsu_Html_Helper_Image {
	
	public static function getPath($netPath, $width = null, $height = null, $boxed = 0) {
		
		$imageSize = getimagesize(self :: fullPath($netPath));
		
		$return = '/image/';
		$return .= ($width == null ? $imageSize[0] : $width) . '/';
		$return .= ($height == null ? $imageSize[1] : $height) . '/';
		$return .= $boxed . '/' . $netPath;
		
		return $return;
	}
	
	public static function getHtml($netPath, $width = null, $height = null, $boxed = 0, $atts = null) {
		
		self :: dimension($netPath, $width, $height, $boxed);

		$path = self :: getPath($netPath, $width, $height, $boxed);
		
		$return = '<img src="' . $path . '" width="' . $width . '" height="' . $height . '"';
		if ($atts != null) {
			foreach ($atts as $key => $value) {
				$return .= ' ' . $key . '="' . $value . '"';
			}
		}
		$return .= ' />';
		
		return $return;
	}
	
	public static function dimension($netPath, & $width, & $height, $boxed) {
		
		$imageSize = getimagesize(self :: fullPath($netPath));
		$srcWidth = $imageSize[0];
		$srcHeight = $imageSize[1];
		
		if ($width == null && $height == null) {
			$width = $srcWidth;
			$height = $srcHeight;
		}

		if ($boxed > 0) {
			$targetWidth = $width == null ? $height : $width;
			$targetHeight = $height == null ? $width : $height;
		} else {
			$width = $width == null ? 10000 : $width;
			$height = $height == null ? 10000 : $height;
			$slopeSource = $srcHeight / $srcWidth;
			$slopeTarget = $height / $width;
			if ($slopeSource < $slopeTarget) {
				$targetWidth = (int) $width;
				$targetHeight = ($targetWidth / $srcWidth) * $srcHeight;
			} else {
				$targetHeight = (int) $height;
				$targetWidth = ($targetHeight / $srcHeight) * $srcWidth;
			}
		}
		
		$width = (int) round($targetWidth);
		$height = (int) round($targetHeight);		
	}
	
	protected static function fullPath($netPath) {
		
		$basePath = Aitsu_Registry :: get()->config->dir->image->basePath;
		
		if (substr($basePath, -1, 1) == '/') {
			$basePath = substr($basePath, 0, strlen($basePath) - 1);
		}
		
		return $basePath . '/' . $netPath;
	}
}
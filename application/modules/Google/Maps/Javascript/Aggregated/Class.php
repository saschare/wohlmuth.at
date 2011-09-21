<?php


/*
 * Development in progress. Please do not use in production environment.
 * 2011-09-21 / A. Kummer
 */

/**
 * Google Maps Javascript API implementation.
 * 
 * This module aggregates single Google Maps into one with multiple markers. If the 
 * source articles contains a HTML area with an index of 'Google.Maps.InfoWindow'
 * the content in that HTML area is used to display an information window.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 * 
 * @link http://code.google.com/apis/maps/documentation/javascript/basics.html Google Maps Basics
 * @link http://code.google.com/apis/maps/documentation/javascript/overlays.html Google Maps Overlays
 */
class Module_Google_Maps_Javascript_Aggregated_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$view = $this->_getView();
		$view->id = uniqid('GoogleMap');

		$width = Aitsu_Content_Config_Text :: set($this->_index, 'GoogleMapJavascript.Width', Aitsu_Translate :: translate('Width'), Aitsu_Translate :: translate('Style'));
		$height = Aitsu_Content_Config_Text :: set($this->_index, 'GoogleMapJavascript.Height', Aitsu_Translate :: translate('Height'), Aitsu_Translate :: translate('Style'));
		$zoom = Aitsu_Content_Config_Text :: set($this->_index, 'GoogleMapJavascript.Zoom', Aitsu_Translate :: translate('Zoom'), Aitsu_Translate :: translate('View'));

		$view->className = Aitsu_Content_Config_Text :: set($this->_index, 'GoogleMapJavascript.Class', Aitsu_Translate :: translate('Class'), Aitsu_Translate :: translate('Style'));
		$type = Aitsu_Content_Config_Radio :: set($this->_index, 'GoogleMapJavascript.Type', Aitsu_Translate :: translate('Type'), array (
			'Roadmap' => 'ROADMAP',
			'Satelite' => 'SATELLITE',
			'Hybrid' => 'HYBRID',
			'Terrain' => 'TERRAIN'
		), Aitsu_Translate :: translate('Display type'));

		$view->type = empty ($type) ? empty ($this->_params->type) ? 'SATELLITE' : $this->_params->type : $type;

		$view->width = empty ($width) ? empty ($this->_params->width) ? 300 : $this->_params->width : $width;
		$view->height = empty ($height) ? empty ($this->_params->height) ? 200 : $this->_params->height : $height;
		$view->zoom = empty ($zoom) ? empty ($this->_params->zoom) ? 0 : $this->_params->zoom : $zoom;

		$output = $view->render('index.phtml');

		Aitsu_Util_Javascript :: addReference('http://maps.google.com/maps/api/js?sensor=false');
		Aitsu_Util_Javascript :: add('init' . $view->id . '();');

		return $output;
	}

}
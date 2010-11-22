<?php


/**
 * Google Maps Javascript API implementation.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19936 2010-11-18 16:45:59Z akm $}
 */

class Module_Google_Maps_Javascript_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'GoogleMapsJavascript',
			'description' => Aitsu_Translate :: translate('Google Maps Javascript API implementation.'),
			'type' => array (
				'Content'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ce513e6-3c5c-4b08-b579-09f97f000101'
		);
	}

	public static function init($context) {

		$instance = new self();

		$view = $instance->_getView();

		$view->id = uniqid('GoogleMap');

		$address = Aitsu_Ee_Config_Text :: set($context['index'], 'GoogleMapJavascript.Address', Aitsu_Translate :: translate('Address'), Aitsu_Translate :: translate('Location'));
		$width = Aitsu_Ee_Config_Text :: set($context['index'], 'GoogleMapJavascript.Width', Aitsu_Translate :: translate('Width'), Aitsu_Translate :: translate('Style'));
		$height = Aitsu_Ee_Config_Text :: set($context['index'], 'GoogleMapJavascript.Height', Aitsu_Translate :: translate('Height'), Aitsu_Translate :: translate('Style'));
		$zoom = Aitsu_Ee_Config_Text :: set($context['index'], 'GoogleMapJavascript.Zoom', Aitsu_Translate :: translate('Zoom'), Aitsu_Translate :: translate('View'));

		$view->className = Aitsu_Ee_Config_Text :: set($context['index'], 'GoogleMapJavascript.Class', Aitsu_Translate :: translate('Class'), Aitsu_Translate :: translate('Style'));
		$view->name = Aitsu_Ee_Config_Text :: set($context['index'], 'GoogleMapJavascript.Name', Aitsu_Translate :: translate('Name'), Aitsu_Translate :: translate('Location'));
		$type = Aitsu_Ee_Config_Radio :: set($context['index'], 'GoogleMapJavascript.Type', Aitsu_Translate :: translate('Type'), array (
			'Roadmap' => 'ROADMAP',
			'Satelite' => 'SATELLITE',
			'Hybrid' => 'HYBRID',
			'Terrain' => 'TERRAIN'
		), Aitsu_Translate :: translate('Display type'));
		$view->type = empty($type) ? 'SATELLITE' : $type;
		$view->address = empty ($address) ? 'Nowhere Creek, Victoria, Australia' : $address;
		$view->width = empty ($width) ? 300 : $width;
		$view->height = empty ($height) ? 200 : $height;
		$view->zoom = empty ($zoom) ? 0 : $zoom;

		$output = $view->render('index.phtml');

		Aitsu_Util_Javascript :: addReference('http://maps.google.com/maps/api/js?sensor=false');
		Aitsu_Util_Javascript :: add('init' . $view->id . '();');

		return $output;
	}

}
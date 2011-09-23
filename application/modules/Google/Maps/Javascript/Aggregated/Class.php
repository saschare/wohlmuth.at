<?php


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
class Module_Google_Maps_Javascript_Aggregated_Class extends Aitsu_Module_Abstract {

	protected $_isVolatile = true;

	protected function _init() {

		Aitsu_Util_Javascript :: addReference('http://maps.google.com/maps/api/js?sensor=false');
		Aitsu_Util_Javascript :: add('initGoogleMapAgg' . $this->_index . '();');
	}

	protected function _main() {

		$view = $this->_getView();
		$view->id = 'GoogleMapAgg' . $this->_index;

		$availableTags = Aitsu_Db :: fetchCol('' .
		'select distinct ' .
		'	 tag.tag ' .
		'from _art_lang artlang ' .
		'left join _art art on artlang.idart = art.idart ' .
		'left join _tag_art tagart on art.idart = tagart.idart ' .
		'left join _tag tag on tag.tagid = tagart.tagid ' .
		'left join _art_geolocation g on artlang.idartlang = g.idartlang ' .
		'where ' .
		'	art.idclient = :idclient ' .
		'	and tag.tag is not null ' .
		'	and g.idartlang is not null ' .
		'order by ' .
		'	tag.tag asc ', array (
			':idclient' => Aitsu_Registry :: get()->session->currentClient
		));

		if ($availableTags) {
			$tags = array ();
			foreach ($availableTags as $tag) {
				$tags[$tag] = $tag;
			}
			$tags = Aitsu_Content_Config_Checkbox :: set($this->_index, 'Google.Maps.Agg.Tags', '', $tags, Aitsu_Translate :: translate('Tags'));
		} else {
			$tags = array ();
		}

		$cat = Aitsu_Content_Config_Link :: set($this->_index, 'Google.Maps.Agg.Cat', Aitsu_Translate :: translate('Category'), Aitsu_Translate :: translate('Category'));

		$width = Aitsu_Content_Config_Text :: set($this->_index, 'GoogleMapJavascript.Width', Aitsu_Translate :: translate('Width'), Aitsu_Translate :: translate('View'));
		$height = Aitsu_Content_Config_Text :: set($this->_index, 'GoogleMapJavascript.Height', Aitsu_Translate :: translate('Height'), Aitsu_Translate :: translate('View'));
		$zoom = Aitsu_Content_Config_Text :: set($this->_index, 'GoogleMapJavascript.Zoom', Aitsu_Translate :: translate('Zoom'), Aitsu_Translate :: translate('View'));

		$type = Aitsu_Content_Config_Radio :: set($this->_index, 'GoogleMapJavascript.Type', Aitsu_Translate :: translate('Type'), array (
			'Roadmap' => 'ROADMAP',
			'Satelite' => 'SATELLITE',
			'Hybrid' => 'HYBRID',
			'Terrain' => 'TERRAIN'
		), Aitsu_Translate :: translate('View'));

		$view->type = empty ($type) ? empty ($this->_params->type) ? 'SATELLITE' : $this->_params->type : $type;

		$view->width = empty ($width) ? empty ($this->_params->width) ? 300 : $this->_params->width : $width;
		$view->height = empty ($height) ? empty ($this->_params->height) ? 200 : $this->_params->height : $height;
		$view->zoom = empty ($zoom) ? empty ($this->_params->zoom) ? 0 : $this->_params->zoom : $zoom;

		$l = Aitsu_Aggregation_Article :: factory();
		$l->havingTags($tags);
		$l->whereBeneathCategory(preg_replace('/[^0-9]/', '', $cat));
		$l->addFilter('coord.lat is not null');
		$l->addFilter('coord.lng is not null');

		$view->locations = $l->fetch();

		$view->clat = 0;
		$view->clng = 0;
		$count = count($view->locations);
		if ($count > 0) {
			foreach ($view->locations as $location) {
				$view->clat += $location->lat / $count;
				$view->clng += $location->lng / $count;
			}
		}

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}

}
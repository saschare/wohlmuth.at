<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Header_Meta_Geo_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    protected function _main() {

        $view = $this->_getView();

        $json = Aitsu_Db::fetchOne('' .
                        'select ' .
                        '   ggl.jsonresponse ' .
                        'from' .
                        '   _art_geolocation as agl ' .
                        'left join ' .
                        '   _google_geolocation as ggl on ggl.id = agl.idlocation ' .
                        'where ' .
                        '   agl.idartlang =:idartlang ' .
                        '', array(
                    ':idartlang' => Aitsu_Registry::get()->env->idartlang
        ));

        if (empty($json)) {
            return '';
        }

        $location = json_decode($json);

        foreach ($location->results[0]->address_components as $address_component) {
            $type = $address_component->types[0];

            $view->$type = new stdClass();
            $view->$type->long_name = $address_component->long_name;
            $view->$type->short_name = $address_component->short_name;
        }

        $view->lat = $location->results[0]->geometry->location->lat;
        $view->lng = $location->results[0]->geometry->location->lng;

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return Aitsu_Util_Date::secondsUntilEndOf('year');
    }

}
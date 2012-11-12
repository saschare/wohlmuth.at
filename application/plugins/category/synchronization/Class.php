<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class SynchronizationCategoryController extends Aitsu_Adm_Plugin_Controller {

    const ID = '50a125aa-0868-4399-9c8b-53777f000001';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register($idcat) {

        $lngCnt = count(Aitsu_Persistence_Language::getAsArray());
                
        if ($lngCnt === 1) {
            $pos = 0;
        } elseif (empty($idcat)) {
            $pos = 1;
        } else {
            $pos = self :: getPosition($idcat, 'synchronization', 'category');
        }

        return (object) array(
                    'name' => 'synchronization',
                    'tabname' => Aitsu_Translate :: translate('Synchronization'),
                    'enabled' => $pos,
                    'position' => $pos,
                    'id' => self :: ID
        );
    }

    public function indexAction() {

        $this->view->idcat = $this->getRequest()->getParam('idcat');
    }

}
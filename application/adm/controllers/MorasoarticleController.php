<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */
class MorasoarticleController extends Zend_Controller_Action {

    public function init() {

        if (!Aitsu_Adm_User :: getInstance()->isAllowed(array(
                    'area' => 'article'
                ))) {
            throw new Exception('Access denied');
        }

        if ($this->getRequest()->getParam('ajax')) {
            $this->_helper->layout->disableLayout();
        }
    }

    public function duplicateAction() {

        $idart = $this->getRequest()->getParam('idart');

        try {
            $newIdArt = Moraso_Article::getInstance($idart)->duplicate();
            $this->_helper->json((object) array(
                        'success' => true,
                        'message' => Aitsu_Translate :: translate('Article duplicated') . ' [idart-' . $idart . ' => idart-' . $newIdArt . ']'
            ));
        } catch (Exception $e) {
            $this->_helper->json(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
    }

}
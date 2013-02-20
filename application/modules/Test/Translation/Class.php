<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Test_Renderonly_Class extends Aitsu_Module_Abstract {

    protected $_renderOnlyAllowed = true;

    protected function _main() {

        return $this->_getView()->render('index.phtml');
    }

}
<?php

/**
 * @author Andreas Kummer <a.kummer@wdrei.ch>
 * @copyright (c) 2013, Andreas Kummer
 */
class Module_Test_Translation_Class extends Aitsu_Module_Abstract {

    protected function _main() {

        return Aitsu_Translate::translate('lastname');
    }

}
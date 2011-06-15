<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class toDoListDashboardController extends Aitsu_Adm_Plugin_Controller {
    const ID = '4da983e3-2cd0-449a-90ad-24117f000101';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register() {

        return (object) array(
            'name' => 'toDoList',
            'tabname' => Aitsu_Translate :: _('To-do-List'),
            'enabled' => true,
            'id' => self :: ID
        );
    }

    public function indexAction() {

    }

    public function storeAction() {

        $data = Aitsu_Db::fetchAll("
            SELECT
                `todo`.`title`,
                `todo`.`description`,
                `todo`.`duedate`,
                `art`.`pagetitle`,
                `art`.`idart`,
                NOW() AS `today`
            FROM
                `_todo` AS `todo`
            INNER JOIN
                `_art_lang` AS `art` ON `art`.`idartlang` = `todo`.`idartlang`
            WHERE (
                `todo`.`duedate` < 'NOW()'
                OR
                `todo`.`status` =0
            )
            AND
                `todo`.`userid` =:userid
            ORDER BY
                `todo`.`duedate` ASC
            ", array(
                ':userid' => Aitsu_Adm_User::getInstance()->userid
            ));

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

}
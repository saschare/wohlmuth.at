<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Adm_Script_ActualizeRelations extends Aitsu_Adm_Script_Abstract {

	protected $_medium = null;

	public static function getName() {

		return Aitsu_Translate :: translate('Actualize relations based on tags');
	}

	public function doCreateTable() {

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _art_rel (' .
		'`sidart` int(10) unsigned NOT NULL,' .
		'`tidart` int(10) unsigned NOT NULL,' .
		'`grade` int(10) unsigned NOT NULL,' .
		'PRIMARY KEY (`sidart`,`tidart`),' .
		'KEY `grade` (`grade`)' .
		') ENGINE=InnoDB DEFAULT CHARSET=utf8');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), 'art_rel'));
	}

	public function doActualize() {

		Aitsu_Db :: query('' .
		'insert into _art_rel ' .
		'(sidart, tidart, grade) ' .
		'select ' .
		'	s.idart as sidart, ' .
		'	t.idart as tidart, ' .
		'	count(distinct t.tagid) as grade ' .
		'from _tag_art s ' .
		'left join _tag_art t on s.tagid = t.tagid ' .
		'group by ' .
		'	s.idart, ' .
		'	t.idart ' .
		'having ' .
		'	count(t.idart) > 0');

		return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('Article relations have been actualized.'));
	}

}
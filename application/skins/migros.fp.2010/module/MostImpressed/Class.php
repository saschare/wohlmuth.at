<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17663 2010-07-21 13:30:22Z akm $}
 */

class Skin_Module_MostImpressed_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('MostImpressed', true);

		$instance = new self();
		$view = $instance->_getView();

		$output = '';
		if ($instance->_get('MostImpressed', $output)) {
			return $output;
		}

		$articles = array ();
		$parentCats = Aitsu_Db :: fetchCol('' .
		'select parent.idcat ' .
		'from _cat as child ' .
		'left join _cat as parent on child.lft between parent.lft and parent.rgt and parent.idclient = child.idclient ' .
		'where ' .
		'	child.idcat = :idcat ' .
		'order by ' .
		'	parent.lft desc ', array (
			':idcat' => Aitsu_Registry :: get()->env->idcat
		));

		while (count($articles) < 5 && ($next = each($parentCats))) {

			$articles = Aitsu_Persistence_Hit :: getMostImpressedByBranch($next['value'], 10, 5);
		}

		$view->articles = $articles;

		$output = $view->render('index.phtml');

		$instance->_save($output, 24 * 60 * 60);

		return $output;
	}

}
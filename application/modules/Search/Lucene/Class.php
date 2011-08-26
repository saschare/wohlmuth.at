<?php


/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Search_Lucene_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Search.Lucene', true);

		Aitsu_Registry :: setExpireTime(0);

		$searchterm = $_REQUEST['searchterm'];

		$view = $this->_getView();

		$view->searchterm = $searchterm;

		$search_area = Aitsu_Config :: get('search.lucene.area');

		$search_array = array ();
		foreach ($search_area as $idcat) {
			$search_array[] = $idcat;
		}

		try {
			$view->results = Aitsu_Lucene_Index :: find($searchterm, $search_array);
		} catch (Exception $e) {
			$view->results = array ();
		}

		$output = $view->render('index.phtml');

		return $output;
	}

}
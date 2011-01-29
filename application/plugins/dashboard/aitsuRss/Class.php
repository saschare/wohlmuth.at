<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class aitsuRssDashboardController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cd045c4-0834-457a-910c-173b7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register() {

		return (object) array (
			'name' => 'aitsuRss',
			'tabname' => Aitsu_Translate :: _('aitsu RSS'),
			'enabled' => true,
			'id' => self :: ID
		);
	}

	public function indexAction() {

	}

	public function rssAction() {

		$cache = Aitsu_Core_Cache :: getInstance('feedsFeedburderComAitsu');
		if ($cache->isValid()) {
			$channel = unserialize($cache->load());
		} else {
			$channel = new Zend_Feed_Rss('http://feeds.feedburner.com/aitsu');
			$cache->setLifetime(60 * 60 * 24);
			$cache->save(serialize($channel));
		}

		$data = array ();
		foreach ($channel as $item) {
			$data[] = (object) array (
				'title' => $item->title(),
				'description' => $item->description()
			);
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}
}
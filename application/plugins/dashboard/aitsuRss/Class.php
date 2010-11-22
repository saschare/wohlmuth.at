<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19619 2010-11-02 16:36:22Z akm $}
 */

class aitsuRssDashboardController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cd045c4-0834-457a-910c-173b7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
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

		$cache = Aitsu_Core_Cache :: getInstance('feedsFeedburderComAitsu');
		if ($cache->isValid()) {
			$channel = unserialize($cache->load());
		} else {
			$channel = new Zend_Feed_Rss('http://feeds.feedburner.com/aitsu');
			$cache->setLifetime(60 * 60 * 24);
			$cache->save(serialize($channel));
		}
		
		$this->view->channel = $channel;
	}
}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class PerformanceAnalyzerDashboardController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cd12585-4714-4e11-92ae-0a567f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register() {

		return (object) array (
			'name' => 'performanceAnalyzer',
			'tabname' => Aitsu_Translate :: _('Site performance'),
			'enabled' => true,
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->overall = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	sum(n) as n, ' .
		'	sum(tsum) / sum(n) as avg, ' .
		'	min(fastest) as fastest, ' .
		'	max(slowest) as slowest, ' .
		'	sqrt(1/(sum(n) - 1) * (sum(tssum) - pow(sum(tsum), 2)/sum(n))) as sigma ' .
		'from _performance_profile ' .
		'where obsdate > date_sub(now(), interval 10 day)');

		$idartlang = Aitsu_Db :: fetchOne('' .
		'select idartlang ' .
		'from _performance_profile ' .
		'where obsdate > date_sub(now(), interval 10 day) ' .
		'group by idartlang ' .
		'order by sum(tsum)/n desc ' .
		'limit 0, 1');

		$this->view->slowest = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	sum(n) as n, ' .
		'	sum(tsum) / sum(n) as avg, ' .
		'	min(fastest) as fastest, ' .
		'	max(slowest) as slowest, ' .
		'	sqrt(1/(sum(n) - 1) * (sum(tssum) - pow(sum(tsum), 2)/sum(n))) as sigma ' .
		'from _performance_profile ' .
		'where ' .
		'	obsdate > date_sub(now(), interval 10 day) ' .
		'	and idartlang = :idartlang ', array (
			':idartlang' => $idartlang
		));

		$details = Aitsu_Db :: fetchRow('' .
		'select distinct ' .
		'	artlang.pagetitle, ' .
		'	artlang.urlname, ' .
		'	catlang.url ' .
		'from _art_lang as artlang ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'left join _cat_lang as catlang on catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang ' .
		'limit 0, 1', array (
			':idartlang' => $idartlang
		));
		$this->view->slowest['url'] = Aitsu_Registry :: get()->config->sys->webpath . $details['url'] . '/' . $details['urlname'] . '.html';
		$this->view->slowest['pagetitle'] = $details['pagetitle'];
		$this->view->slowest['idartlang'] = $idartlang;
	}
}
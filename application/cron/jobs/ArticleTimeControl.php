<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class CronJob_ArticleTimeControl extends Aitsu_Cron_Job_Abstract {

	protected function _isPending($lasttime) {

		return true;
	}

	protected function _exec() {
		
		Aitsu_Application_Status :: isEdit(true);

		$articles = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.idartlang, ' .
		'	artlang.idart, ' .
		'	artlang.idlang, ' .
		'	if(timea <= now(), statusa, null) exa, ' .
		'	if(timeb <= now(), statusb, null) exb ' .
		'from  _art_timecontrol tc ' .
		'left join _art_lang artlang on tc.idartlang = artlang.idartlang ' .
		'where ' .
		'	timea <= now() ' .
		'	or timeb <= now() ');

		if (!$articles) {
			return;
		}

		foreach ($articles as $art) {
			$art = (object) $art;
			$article = Aitsu_Persistence_Article :: factory($art->idart, $art->idlang)->load();
			
			if ($art->exa != null) {
				Aitsu_Db :: query('' .
				'update _art_timecontrol set ' .
				'	timea = null ' .
				'where ' .
				'	idartlang = :idartlang', array (
					':idartlang' => $art->idartlang
				));
				if ($art->exa == 0 || $art->exa == 1) {
					$article->online = $art->exa;
				}
			}
			if ($art->exb != null) {
				Aitsu_Db :: query('' .
				'update _art_timecontrol set ' .
				'	timeb = null ' .
				'where ' .
				'	idartlang = :idartlang', array (
					':idartlang' => $art->idartlang
				));
				if ($art->exb == 0 || $art->exb == 1) {
					$article->online = $art->exb;
				}
			}
			
			$article->save();
			$article->publish(true, true);
		}
		
	}

}
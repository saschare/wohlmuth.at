<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Bootstrap_EvalRequest {

	protected $redirectStep;

	public static function run() {

		$instance = new self();
		$session = Aitsu_Registry :: get()->session;

		/*
		 * Set lang and locale.
		 */
		if (!isset ($session->lang)) {
			$session->locale = Aitsu_Registry :: get()->config->defaultLocale;
			$session->lang = Aitsu_Registry :: get()->config->sys->language;
		}

		if (isset ($_GET['changelang'])) {
			$session->locale = $instance->_getLang();
			$session->lang = $_GET['changelang'];
		}

		/*
		 * Evaluate the request, identify the requested idartlang.
		 */
		if (isset ($_GET['idart']) || isset ($_GET['idcat'])) {
			Aitsu_Registry :: get()->env->idart = empty ($_GET['idart']) ? null : $_GET['idart'];
			Aitsu_Registry :: get()->env->idcat = empty ($_GET['idcat']) ? null : $_GET['idcat'];
			Aitsu_Registry :: get()->env->idlang = empty ($_GET['lang']) ? $session->lang : $_GET['lang'];
		}

		if (!isset ($_GET['idart']) && !isset ($_GET['idcat']) && !isset ($_GET['url'])) {
			/*
			 * No article is specified. We therefore use the configured home page.
			 */
			Aitsu_Registry :: get()->env->idart = null;
			Aitsu_Registry :: get()->env->idcat = Aitsu_Registry :: get()->config->sys->startidcat;
			Aitsu_Registry :: get()->env->idlang = empty ($_GET['lang']) ? $session->lang : $_GET['lang'];
		}
		if (Aitsu_Registry :: get()->config->rewrite->modrewrite) {
			$obj = call_user_func(array (
				Aitsu_Registry :: get()->config->rewrite->controller,
				'getInstance'
			));
			try {
				$content = $obj->registerParams();
			} catch (Zend_Db_Statement_Exception $e) {
				echo $e->getMessage();
				exit ();
			}
		}

		if (isset (Aitsu_Registry :: get()->env->idart)) {
			/*
			 * Evaluation based on idart.
			 */
			$instance->_setIdartlang(Aitsu_Registry :: get()->env->idart);
		}
		elseif (isset (Aitsu_Registry :: get()->env->idcat) && !isset ($_GET['artname'])) {
			/*
			 * Evaluation based on idcat.
			 */
			$instance->_setIdartlang(null, Aitsu_Registry :: get()->env->idcat);
		}

		if (!isset (Aitsu_Registry :: get()->env->idartlang)) {
			$altUrl = Aitsu_Ee_Rewrite_History :: getInstance()->getAlternative();
			if ($altUrl !== false) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: {$altUrl}");
				exit (0);
			}
		}

		if (!isset (Aitsu_Registry :: get()->env->idartlang)) {
			if (!isset (Aitsu_Registry :: get()->env->idlang)) {
				Aitsu_Registry :: get()->env->idlang = Aitsu_Registry :: get()->config->sys->language;
			}
			header("HTTP/1.0 404 Not Found");
			Aitsu_Registry :: get()->env->idart = Aitsu_Registry :: get()->config->sys->errorpage;
			$instance->_setIdartlang(Aitsu_Registry :: get()->env->idart);
		}

		/*
		 * Resolve redirects.
		 */
		$instance->_resolveRedirects();

		/*
		 * Save URL / idartlang combination for seo purposes.
		 */
		Aitsu_Ee_Rewrite_History :: getInstance()->saveUrl();

		/*
		 * Set the appropriate locale, taken from the database or de_DE, if no
		 * locale is specified.
		 */
		try {
			$locale = Aitsu_Db :: fetchOneC('eternal', '' .
			'select locale from _lang where idlang = :idlang', array (
				':idlang' => Aitsu_Registry :: get()->env->idlang
			));
		} catch (Exception $e) {
			$locale = 'de';
		}
		$locale = strlen(trim($locale)) == 0 ? 'de' : $locale;
		$locale = new Zend_Locale($locale);
		Aitsu_Registry :: get()->env->locale = $locale;
	}

	protected function _getLang() {

		$idlang = (int) $_GET['changelang'];

		$languageString = Aitsu_Db :: fetchOne("" .
		"select name from _lang where idlang = ? " .
		"", array (
			$idlang
		));

		if (preg_match('/^[a-z]{2}(?:_[A-Z]{2})?$/', $languageString)) {
			return $languageString;
		}

		/*
		 * The pattern used is not a local. Now we try to resolve the language name.
		 */

		if (strlen($languageString) == 2) {
			/*
			 * The language string contains two characters. It is assumed that this
			 * is a locale if converted to lower case.
			 */
			return strtolower($languageString);
		}

		if (strpos(strtolower($languageString), 'deutsch') !== false || strpos(strtolower($languageString), 'german') !== false) {
			return 'de';
		}

		if (strpos(strtolower($languageString), 'engli') !== false) {
			return 'en';
		}

		if (strpos(strtolower($languageString), 'franz') !== false || strpos(strtolower($languageString), 'french') !== false) {
			return 'fr';
		}

		if (strpos(strtolower($languageString), 'span') !== false) {
			return 'es';
		}

		if (strpos(strtolower($languageString), 'ital') !== false) {
			return 'de';
		}
	}

	protected function _setIdartlang($idart = null, $idcat = null) {

		$reg = Aitsu_Registry :: get();

		if ($idart != null) {
			$results = Aitsu_Db :: fetchAllC('eternal', '' .
			'select ' .
			'	artlang.idartlang as idartlang, ' .
			'	catart.idcat as idcat, ' .
			'	catlang.public ' .
			'from _art_lang as artlang ' .
			'left join _cat_art as catart on artlang.idart = catart.idart ' .
			'left join _cat_lang as catlang on catart.idcat = catlang.idcat and catlang.idlang = artlang.idlang ' .
			'where ' .
			'	artlang.idart = :idart ' .
			'	and artlang.idlang = :idlang ' .
			'	and artlang.online = 1 ' .
			'', array (
				':idart' => $idart,
				':idlang' => $reg->env->idlang
			));

			if ($results) {
				$reg->env->idartlang = $results[0]['idartlang'];
				$reg->env->idcat = $results[0]['idcat'];
				$reg->env->ispublic = $results[0]['public'];
			} else {
				$reg->env->idartlang = null;
			}
			return;
		}

		$results = Aitsu_Db :: fetchAllC(60 * 60, '' .
		'select ' .
		'	catlang.startidartlang as idartlang, ' .
		'	artlang.idart as idart, ' .
		'	catlang.public ' .
		'from _cat_lang as catlang ' .
		'left join _art_lang as artlang on catlang.startidartlang = artlang.idartlang ' .
		'where ' .
		'	catlang.idcat = :idcat ' .
		'	and catlang.idlang = :idlang ' .
		'	and artlang.online = 1 ' .
		'', array (
			':idcat' => $idcat,
			':idlang' => $reg->env->idlang
		));

		if ($results) {
			$reg->env->idartlang = $results[0]['idartlang'];
			$reg->env->idart = $results[0]['idart'];
			$reg->env->ispublic = $results[0]['public'];
		} else {
			$reg->env->idartlang = null;
		}
	}

	protected function _resolveRedirects() {

		if ($this->redirectStep > 5) {
			/*
			 * Break rule 1:
			 * More than 5 redirections in a row are not allowed. We assume a circle and
			 * therefore show a 404 header and redirect to the error page.
			 */
			header("HTTP/1.0 404 Not Found");
			Aitsu_Registry :: get()->env->idart = Aitsu_Registry :: get()->config->errordoc;
			$this->_setIdartlang(Aitsu_Registry :: get()->env->idart);
			return;
		}

		$this->redirectStep++;

		$url = Aitsu_Db :: fetchOneC(60 * 60, "" .
		"select " .
		"	redirect_url " .
		"from _art_lang " .
		"where " .
		"	idartlang = :idlang " .
		"	and redirect = 1 " .
		"", array (
			':idlang' => Aitsu_Registry :: get()->env->idartlang
		));

		if (!$url) {
			/*
			 * Break rule 2:
			 * No redirect has to be made.
			 */
			return;
		}

		if (!isset (Aitsu_Registry :: get()->meta)) {
			Aitsu_Registry :: get()->meta = array (
				'robots' => 'noindex'
			);
		} else {
			Aitsu_Registry :: get()->meta['robots'] = 'noindex';
		}

		if (!preg_match('/^(idart|idcat){1}\\s*(\\d*)\\s*$/', $url, $match)) {
			/*
			 * Break rule 3:
			 * No matching pattern found. The URL is assumed to be valid
			 * and therefore a header redirect is made to specified URL. 
			 */
			header("Location: $url");
			exit ();
		}

		if (strtolower($match[1]) == 'idart') {
			Aitsu_Registry :: get()->env->idcat = null;
			Aitsu_Registry :: get()->env->idart = $match[2];
			$this->_setIdartlang($match[2]);
		} else {
			Aitsu_Registry :: get()->env->idart = null;
			Aitsu_Registry :: get()->env->idcat = $match[2];
			$this->_setIdartlang(null, $match[2]);
		}

		if (!isset (Aitsu_Registry :: get()->env->idartlang)) {
			/*
			 * Break rule 4:
			 * The target article is either not exiting or set to offline.
			 * We therefore redirect to the error page.
			 */
			header("HTTP/1.0 404 Not Found");
			Aitsu_Registry :: get()->env->idart = Aitsu_Registry :: get()->config->sys->errorpage;
			$this->_setIdartlang(Aitsu_Registry :: get()->env->idart);
			return;
		}

		/*
		 * Recursive invocation of the current method until one of the specifed
		 * four break rules apply:
		 * (1) number of redirects exceeds 5 (error)
		 * (2) target article does not redirect (preferred)
		 * (3) URL is assumed to be a header redirect (strange, but possible)
		 * (4) target article does not exist or is set to offline (error)
		 */
		$this->_resolveRedirects();
	}

	protected function _checkUserPermissions() {

		if (isset (Aitsu_Registry :: get()->env->ispublic) && Aitsu_Registry :: get()->env->ispublic == 1) {
			/*
			 * No permission check necessary. Return.
			 */
			return;
		}

		$user = Aitsu_Adm_User :: getInstance();

		if ($user != null && $user->isAllowed(array (
				'language' => Aitsu_Registry :: get()->env->idlang,
				'area' => 'frontend',
				'action' => 'view',
				'resource' => array (
					'type' => 'cat',
					'id' => Aitsu_Registry :: get()->env->idcat
				)
			))) {
			return;
		}

		/*
		 * The user seems not to be allowed to access the page. We therefore
		 * give him the possiblity to log in.
		 */
		Aitsu_Registry :: get()->env->idart = Aitsu_Registry :: get()->config->sys->loginpage;
		$this->_setIdartlang(Aitsu_Registry :: get()->config->sys->loginpage);
	}
}
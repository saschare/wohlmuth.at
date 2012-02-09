<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Cli_Search404 extends Aitsu_Cli_Script_Abstract {

	protected function _main() {

		if (!isset ($this->_options['e'])) {
			echo 'Use -e to provide the environment' . "\n";
			exit (0);
		}

		Aitsu_Application_Status :: isEdit(false);

		Aitsu_Registry :: get()->config = Aitsu_Config_Ini :: getInstance('clients/' . $this->_options['e']);

		$pages = Aitsu_Db :: fetchAll('' .
		'select distinct ' .
		'	artlang.idartlang, ' .
		'	concat(catlang.url, \'/\', artlang.urlname) url ' .
		'from _art_lang artlang ' .
		'left join _cat_art catart on artlang.idart = catart.idart ' .
		'left join _cat_lang catlang on catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang ' .
		'where ' .
		'	artlang.online = 1 ' .
		'	and catlang.public = 1', array (
			':client' => Aitsu_Config :: get('sys.client')
		));

		$client = new Zend_Http_Client();
		$client->setConfig(array (
			'maxredirects' => 0,
			'timeout' => 30
		));

		$uri = Aitsu_Config :: get('sys.webpath');
		$counter = 0;
		foreach ($pages as $page) {
			$counter++;
			$client->setUri($uri . $page['url'] . '.html');
			$start = microtime(true);
			$response = $client->request();
			$period = microtime(true) - $start;
			$status = $response->getStatus();

			$line = $counter . ': ';
			$line .= $status . " - ";
			$line .= $uri . $page['url'] . '.html';
			echo $line . "\n";

			Aitsu_Db :: put('_link_status', 'id', array (
				'ref' => $uri . $page['url'] . '.html',
				'status' => $status,
				'rt' => round($period * 1000)
			));

			if ($response->isError()) {
				break;
			}

			$content = $response->getBody();

			if (preg_match_all('/\\shref=\"([^\"]*)"/', $content, $matches) > 0) {
				for ($i = 0; $i < count($matches[0]); $i++) {
					$src = $this->_rel2abs($matches[1][$i], $uri . $page['url'] . '.html');
					$client->setUri($src);
					$start = microtime(true);
					$response = $client->request();
					$period2 = microtime(true) - $start;
					$status = $response->getStatus();

					$counter++;
					$line = $counter . ': ';
					$line .= $response->getStatus() . " - ";
					$line .= $src;
					echo $line . "\n";

					Aitsu_Db :: put('_link_status', 'id', array (
						'src' => $uri . $page['url'] . '.html',
						'ref' => $src,
						'status' => $response->getStatus(),
						'rt' => round($period2 * 1000)
					));
				}
			}
			if (preg_match_all('/\\ssrc=\"([^\"]*)\"/', $content, $matches) > 0) {
				for ($i = 0; $i < count($matches[0]); $i++) {
					$src = $this->_rel2abs($matches[1][$i], $uri . $page['url'] . '.html');
					$client->setUri($src);
					$start = microtime(true);
					$response = $client->request();
					$period2 = microtime(true) - $start;
					$status = $response->getStatus();

					$counter++;
					$line = $counter . ': ';
					$line .= $response->getStatus() . " - ";
					$line .= $src;
					echo $line . "\n";

					Aitsu_Db :: put('_link_status', 'id', array (
						'src' => $uri . $page['url'] . '.html',
						'ref' => $src,
						'status' => $response->getStatus(),
						'rt' => round($period2 * 1000)
					));
				}
			}
		}
	}

	protected function _rel2abs($rel, $base) {

		if (parse_url($rel, PHP_URL_SCHEME) != '')
			return $rel;

		if ($rel[0] == '#' || $rel[0] == '?')
			return $base . $rel;

		extract(parse_url($base));

		$path = preg_replace('#/[^/]*$#', '', $path);

		if ($rel[0] == '/')
			$path = '';

		$abs = "$host$path/$rel";

		$re = array (
			'#(/\.?/)#',
			'#/(?!\.\.)[^/]+/\.\./#'
		);
		for ($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {
		}

		return $scheme . '://' . $abs;
	}
}
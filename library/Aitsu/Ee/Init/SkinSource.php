<?php


/**
 * SkinSource.
 *
 * @author Christian Kehres, webtischlerei.de
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2010,webtischlerei.de
 * @copyright Copyright &copy; 2011, w3concepts AG
 *
 * {@id $Id: SkinSource.php 19975 2010-11-19 12:44:06Z akm $}
 */
class Aitsu_Ee_Init_SkinSource implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (!isset($_GET['skinsource'])) {
			return;
		}

		if (ob_get_level() > 0) {
			ob_end_clean();
		}

		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			$etag = $_SERVER['HTTP_IF_NONE_MATCH'];
		}
		
		$source = $_GET['skinsource'];
		if (substr($source, 0, 6) == 'admin/') {
			$source = substr($source, 6);
		}

		$exploded = explode("/", $source);

		$expires = 60 * 60 * 24 * 7;

		header("Pragma: public");
		header("Cache-Control: maxage=" . $expires);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

		$file_extension = strtolower(pathinfo(end($exploded), PATHINFO_EXTENSION));

		self :: setContentTypeHeader($file_extension);

		if (isset ($etag)) {
			$content = self :: _cache($etag);
		}

		if (isset($content) && $content) {
			header("HTTP/1.1 304 Not Modified");
			header("Connection: Close");
		} else {
			$location = APPLICATION_PATH . "/skins/" . Aitsu_Registry :: get()->config->skin . "/{$source}";

			if (is_readable($location)) {
				$content = file_get_contents($location);
			} else {
				header("HTTP/1.1 404 Not Found");
				header("Connection: Close");
				exit ();
			}

			if ($file_extension === 'css') {
				$etag = hash('md4', $content);
				self :: _cache($etag, $content, array (
					'css'
				));
			}

			if ($file_extension === 'js') {
				$etag = hash('md4', $content);
				self :: _cache($etag, $content, array (
					'js'
				));
			}

			if (Aitsu_Registry :: get()->config->output->gzhandler) {
				$content = gzencode($content);
				header('Content-Encoding: gzip');
			}

			if (!isset ($etag)) {
				$etag = hash('md4', $content);
			}

			header("ETag: {$etag}");

			echo $content;
		}
		exit (0);
	}

	private function _cache($etag, $content = null, array $tags = null) {
		if (isset ($content)) {
			Aitsu_Cache :: getInstance($etag)->save($content, $tags);
		} else {
			return Aitsu_Cache :: getInstance($etag)->load();
		}
	}

	private static function setContentTypeHeader($file_extension) {

		switch ($file_extension) {
			case 'css' :
				header('Content-type: text/css');
				break;
			case 'js' :
				header('Content-type: application/javascript');
				break;
			case 'png':
				header('Content-type: image/png');
				break;
			case 'gif':
				header('Content-type: image/gif');
				break;
			case 'jpg':
				header('Content-type: image/jpeg');
				break;
			case 'jpeg':
				header('Content-type: image/jpeg');
				break;
                        case 'pdf':
				header('Content-type: application/pdf');
				break;
			default :
				break;
		}
	}

}
<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Flex_Class extends Aitsu_Module_Tree_Abstract {

	protected $_renderOnlyAllowed = true;
	protected $_allowEdit = false;

	protected function _init() {

		$view = $this->_getView();
		$view->id = $this->_index;
		$view->idartlang = Aitsu_Registry :: get()->env->idartlang;
	}

	protected function _main() {

		$this->_saveContent();

		$view = $this->_getView();
		$view->id = $this->_index;
		$view->idartlang = Aitsu_Registry :: get()->env->idartlang;
		$view->availableModules = Aitsu_Config :: get('flex')->toArray();

		$content = $this->_loadContent();

		if (Aitsu_Application_Status :: isEdit()) {
			$parts = $this->_explode($content);
			$view->content = array ();
			for ($i = 0; $i < count($parts); $i++) {
				$view->content[] = (object) array (
					'position' => $i,
					// 'textile' => implode("\n\n", array_slice($parts, $i)),
					'textile' => $parts[$i],
					'html' => Wdrei_Textile :: textile($parts[$i]),
					'isShortcodeBlock' => preg_match('/^\\.sc\\([^\\)]*\\)$/s', trim($parts[$i]))
				);
			}
			return $view->render('index.phtml');
		}

		return Wdrei_Textile :: textile($content);
	}

	protected function _cachingPeriod() {
		/*
		 * 1 year.
		 */
		return 60 * 60 * 24 * 365;
	}

	protected function _saveContent() {

		if (!Aitsu_Application_Status :: isEdit() || !isset ($_POST['edit']) || !$_POST['edit'] == 1) {
			return;
		}

		$parts = preg_split('/(?:\\n\\r?){2,}/s', $this->_loadContent());

		if (isset ($_POST['pos']) && isset ($_POST['content'])) {
			/*
			 * Edit case with textile editor.
			 */
			trigger_error(var_export($parts, true));
			if ($_POST['pos'] == 0) {
				$content = $_POST['content'];
			} else {
				$parts = $this->_explode($this->_loadContent());
				$content = trim(implode("\n\n", array_slice($parts, 0, $_POST['pos'])) . "\n\n" . $_POST['content']);
			}
			trigger_error($content);
			Aitsu_Content :: set($this->_index, Aitsu_Registry :: get()->env->idartlang, $content);
			$content = Aitsu_Content :: get($this->_index, Aitsu_Content :: PLAINTEXT, null, null, 0, true);
			trigger_error($content);
			return;
		}
		elseif (isset ($_POST['del'])) {
			/*
			 * Remove case.
			 */
			$parts = $this->_explode($this->_loadContent());
			unset ($parts[$_POST['del']]);
			$content = trim(implode("\n\n", $parts));
			Aitsu_Content :: set($this->_index, Aitsu_Registry :: get()->env->idartlang, $content);
			Aitsu_Content :: get($this->_index, Aitsu_Content :: PLAINTEXT, null, null, 0, true);
			return;
		}
		elseif (isset ($_POST['newpos'])) {
			/*
			 * Move case.
			 */
			$parts = $this->_explode($this->_loadContent());
			$newOrder = array ();
			foreach ($_POST['newpos'] as $pos) {
				$newOrder[] = $parts[$pos];
			}
			$content = trim(implode("\n\n", $newOrder));
			Aitsu_Content :: set($this->_index, Aitsu_Registry :: get()->env->idartlang, $content);
			Aitsu_Content :: get($this->_index, Aitsu_Content :: PLAINTEXT, null, null, 0, true);
			return;
		}

		return;
	}

	protected function _loadContent() {

		return Aitsu_Content :: get($this->_index, Aitsu_Content :: PLAINTEXT, null, null, 0);
	}

	protected function _explode($text) {

		$returnValue = array ();

		$parts = preg_split('/(?:\\n\\r?){2,}/s', $text);

		for ($i = 0; $i < count($parts); $i++) {
			$part = trim($parts[$i]);
			if (!empty ($part)) {
				$returnValue[] = $part;
			}
		}

		return $returnValue;
	}

}
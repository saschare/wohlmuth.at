<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_Syndication_HTML_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$index = str_replace('_', ' ', $context['index']);
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$instance = new self();

		$output = '';
		if ($instance->_get('SyndicationHtml' . $index, $output)) {
			return $output;
		}

		$fragments = $instance->_getFragmentsArray();
		$fragment = Aitsu_Content_Config_Select :: set($index, 'Syndication.HTML.Fragment', 'Fragment', $fragments, 'Syndication');
		$maxAge = Aitsu_Content_Config_Select :: set($index, 'Syndication.HTML.MaxAge', 'Max. age', array (
			'Default' => '',
			'1 hour' => 60 * 60,
			'2 hours' => 2 * 60 * 60,
			'4 hours' => 4 * 60 * 60,
			'12 hours' => 4 * 60 * 60,
			'1 day' => 24 * 60 * 60,
			'1 week' => 7 * 24 * 60 * 60
		), 'Syndication');
		$maxAge = empty ($maxAge) ? 24 * 60 * 60 : $maxAge;

		if (!empty ($fragment)) {
			$sourceid = strtok($fragment, '-');
			$sourceidartlang = strtok('-');
			$id = strtok("\n");

			if (Aitsu_Db :: fetchOne('' .
				'select count(*) from _syndication_resource_art ' .
				'where ' .
				'	sourceid = :sourceid ' .
				'	and sourceidartlang = :sourceidartlang ' .
				'	and idartlang = :idartlang', array (
					':sourceid' => $sourceid,
					':sourceidartlang' => $sourceidartlang,
					':idartlang' => Aitsu_Registry :: get()->env->idartlang
				)) == 1) {
				$output = Aitsu_Persistence_SyndicationResource :: factory(array (
					$sourceid,
					$sourceidartlang
				))->load($maxAge)->get($id);
			} else {
				/*
				 * The configured resource is not registered with the current article.
				 */
			}
		}

		if (Aitsu_Registry :: isEdit()) {
			$visiblityCheck = trim(html_entity_decode(strip_tags($output), ENT_COMPAT, 'UTF-8'));
			if (empty ($visiblityCheck)) {
				$output = '[[ Syndication.HTML ]]';
			}
			$output = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $output;
		}

		$instance->_save($output, 60 * 60 * 24);

		return $output;
	}

	protected function _getFragmentsArray() {

		if (!Aitsu_Application_Status :: isEdit())
			return array ();

		$resources = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	art.sourceid, ' .
		'	art.sourceidartlang, ' .
		'	res.name ' .
		'from _syndication_resource_art art ' .
		'left join _syndication_resource res on art.sourceid = res.sourceid and art.sourceidartlang = res.sourceidartlang ' .
		'where ' .
		'	art.idartlang = :idartlang', array (
			':idartlang' => Aitsu_Registry :: get()->env->idartlang
		));

		if (!$resources)
			return array ();

		$resData = array ();
		foreach ($resources as $resource) {
			$res = Aitsu_Persistence_SyndicationResource :: factory(array (
				$resource['sourceid'],
				$resource['sourceidartlang']
			))->load(1);
			$resData[] = (object) array (
				'name' => $resource['name'],
				'children' => $res->data,
				'source' => $resource['sourceid'] . '-' . $resource['sourceidartlang']
			);
		}

		$return = array ();
		$this->_fragmentRecursion($return, $resData);

		return $return;
	}

	protected function _fragmentRecursion(& $return, $data, $level = 0, $source = null) {

		if (is_object($data)) {
			$key = isset ($data->name) ? $data->name . ' (' . $source . ')' : str_repeat('_', $level * 2) . ' ' . $data->id . ' (' . $source . ')';
			$return[$key] = $source . '-' . (isset ($data->id) ? $data->id : 'Root');
			$level++;
			foreach ($data->children as $entry) {
				$source = isset ($data->source) ? $data->source : $source;
				$this->_fragmentRecursion($return, $entry, $level, $source);
			}
		} else
			if (is_array($data)) {
				foreach ($data as $entry) {
					$this->_fragmentRecursion($return, $entry, $level, $source);
				}
			}
	}
}
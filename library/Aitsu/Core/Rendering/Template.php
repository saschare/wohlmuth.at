<?php


/**
 * Template rendering.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Template.php 16689 2010-05-27 20:08:06Z akm $}
 * 
 * @deprecated 0.9.2 - 07.10.2010
 */

class Aitsu_Core_Rendering_Template {

	protected $idartlang;
	protected $db;
	protected $modules;
	protected $layout = '';
	protected $context = array();

	protected function __construct($idartlang) {
		
		$this->idartlang = $idartlang;

		$this->db = Aitsu_Registry :: get()->db;
		
		$this->_readTemplate();
	}

	public static function factory($idartlang) {

		$instance = new self($idartlang);

		return $instance;
	}

	protected function _readTemplate() {

		$idtpl = 0;
		$content = array();

		/*
		 * Returns the content saved in the con_content table.
		 */
		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	type.type as type, " .
		"	content.typeid as typeid, " .
		"	content.value as value, " .
		"	concat(upl.dirname, upl.filename) as path " .
		"from _content as content " .
		"left join _type as type on (content.idtype = type.idtype) " .
		"left join _upl as upl on (content.value = upl.idupl) " .
		"where " .
		"	content.idartlang = ? " .
		"", array (
			$this->idartlang
		));
		if ($results) {
			foreach ($results as $result) {
				$tmp = $result;
				if ($result['type'] == 'CMS_IMG') {
					$tmp['value'] = '/' . $result['path'];
				}
				$content[] = $tmp;
			}
		}

		/*
		 * The query returns at least one row, giving us the chance to
		 * read idtpl even there is no configuration made.
		 */
		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	template.idtpl as idtpl, /* id of the template */ " .
		"	conf.number as number, /* container number */" .
		"	conf.container as container /* container content */ " .
		"from _art_lang as artlang " .
		"left join _cat_art as catart on (artlang.idart = catart.idart) " .
		"left join _cat_lang as catlang on (catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang) " .
		"left join _template_conf as tplconf on (tplconf.idtplcfg = if (artlang.idtplcfg > 0, artlang.idtplcfg, catlang.idtplcfg)) " .
		"left join _container_conf as conf on (conf.idtplcfg = tplconf.idtplcfg) " .
		"left join _template as template on (tplconf.idtpl = template.idtpl) " .
		"where " .
		"artlang.idartlang = ? " .
		"", array (
			$this->idartlang
		));
		foreach ($results as $result) {
			$moduleConfiguration[$result['number']] = $result['container'];
		}
		$idtpl = $results[0]['idtpl'];
		
		/*
		 * Returns the container number and the module code of the modules
		 * used in that particular template configuration.
		 */
		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	container.number as number, /* container number */ " .
		"	module.output, /* module code */ " .
		"	module.idmod, " .
		"	module.name " .
		"from _container as container " .
		"left join _mod as module on (container.idmod = module.idmod) " .
		"where " .
		"	container.idmod > 0 " .
		"	and container.idtpl = ? " .
		"", array (
			$idtpl
		));
		if ($results) {
			foreach ($results as $result) {
				$configuration = isset($moduleConfiguration[$result['number']]) ? $moduleConfiguration[$result['number']] : '';
				$this->modules[$result['number']] = new Aitsu_Core_Rendering_Module($result['output'], $configuration, $content, $result['idmod'] . ' (' . $result['name'] . ')');
			}
		}
		
		/*
		 * Returns the layout of the template.
		 */
		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	layout.code " .
		"from _template as template " .
		"left join _lay as layout on (template.idlay = layout.idlay) " .
		"where " .
		"	template.idtpl = ? " .
		"", array (
			$idtpl
		));
		if ($results) {
			$this->layout = preg_replace('|<!--\\s*remove.*?/remove\\s*-->|s', "", $results[0]['code']);
		}		
	}
	
	public function render() {
		
		if (preg_match_all('|<container.*?id=\"([0-9]*).*?</container>|s', $this->layout, $matches) == 0) {
			if (preg_match_all('/CMS_CONTAINER\\[(\\d*)\\]/s', $this->layout, $matches) == 0) { 
				return $this->layout;
			}
		}
		
		$layout = $this->layout;
		
		for ($i = 0; $i < count($matches[0]); $i++) {
			$start = microtime(true);
			$this->context['cCurrentContainer'] = $matches[1][$i];
			$moduleId = isset($this->modules[$matches[1][$i]]) ? $this->modules[$matches[1][$i]]->modulId : null;
			$moduleOutput = isset($this->modules[$matches[1][$i]]) ? $this->modules[$matches[1][$i]]->getOutput($this->context) : '';
			$period = number_format((microtime(true) - $start) * 1000, 2);
			if (isset($_GET['analyze']) && Aitsu_Registry :: get()->config->admin->allowanalyse == true) {
				$moduleOutput .= '<div><strong>Container ' . $matches[1][$i] . ' : ' . $period . ' ms</strong></div>';
			}
			$layout = str_replace($matches[0][$i], $moduleOutput, $layout);
		}
		
		return $layout;
	}
	
	public function setContext($context, $inGlobalScope = false) {
		
		$this->context = $context;
		
		if ($inGlobalScope) {
			foreach ($context as $key => $value) {
				$GLOBALS[$key] = $value;
			}
		}
	}
}
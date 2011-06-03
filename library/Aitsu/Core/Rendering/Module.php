<?php


/**
 * Rendering modules.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Module.php 16699 2010-05-28 08:19:35Z akm $}
 */

class Aitsu_Core_Rendering_Module {
	
	protected $code;
	protected $configuration;
	protected $contents;
	public $modulId;
	
	public function __construct($code, $configuration, & $content, $modulId = null) {
		
		$this->code = $code;
		$this->configuration = $configuration;
		$this->contents = $content;
		$this->modulId = $modulId;
	}
	
	public function getOutput($context = array()) {
		
		$autoCache = null;		
		if (!Aitsu_Registry :: isEdit() && preg_match('@/\\*\\s*aee-autocache\\s*(\\d*)\\s\\*/@', $this->code, $match)) {
			$autoCache = Aitsu_Cache :: getInstance('aee_autocache_' . $context['cCurrentContainer'] . '_' . Aitsu_Registry :: get()->env->idlang . '_' . Aitsu_Registry :: get()->env->idart);
			$autoCache->setLifetime($match[1]);
			$out = $autoCache->load();
			if ($out && !isset($_GET['clearcache'])) {
				return $out;
			}
		}
		
		/*
		 * Replace configuration elements in the module code.
		 */
		if (strlen($this->configuration) > 0) {
			if (preg_match_all('/([0-9]*)=([^&]*)/', $this->configuration, $matches)) {
				for ($i = 0; $i < count($matches[0]); $i++) {
					$this->code = str_replace('"CMS_VALUE[' . $matches[1][$i] . ']"', '$CMS_VALUE[' . $matches[1][$i] . ']', $this->code);
					$CMS_VALUE[$matches[1][$i]] = urldecode($matches[2][$i]);
				}
			}
		}
		
		/*
		 * Replace content elements in the module code.
		 */
		foreach ($this->contents as $content) {
			$this->code = str_replace('"' . $content['type'] . '[' . $content['typeid'] . ']' . '"', '$' . $content['type'] . '[' . $content['typeid'] . ']', $this->code);
			${$content['type']}[$content['typeid']] = urldecode($content['value']);
		}
		
		/*
		 * Replace empty content elements in the module code.
		 */
		$this->code = preg_replace('/("|\')CMS_[A-Z]*\\[[0-9]*\\](\"|\')/', "$1$1", $this->code);
		
		/*
		 * Restore context.
		 */
		foreach ($context as $key => $value) {
			$$key = $value;
		}
		
		try {
			ob_start();
			eval("?>\n" . $this->code);
			$returnValue = ob_get_contents();
			ob_end_clean();
		} catch (Exception $e) {
			$returnValue = $e->getMessage();
		}
		
		if ($autoCache != null) {
			$autoCache->save($returnValue . ' ');
		}
		
		return $returnValue;
	}
}
<?php


/**
 * Article configuration with inheritance.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Config.php 18146 2010-08-16 15:48:49Z akm $}
 */

/*
CREATE TABLE IF NOT EXISTS `con_aitsu_article_property` (
  `propertyid` int(10) unsigned NOT NULL,
  `idartlang` int(10) unsigned NOT NULL,
  `textvalue` text,
  `floatvalue` double DEFAULT NULL,
  `datevalue` datetime DEFAULT NULL,
  PRIMARY KEY (`propertyid`,`idartlang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `con_aitsu_property` (
  `propertyid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `type` enum('text','float','date') NOT NULL,
  PRIMARY KEY (`propertyid`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `con_aitsu_article_property`
ADD CONSTRAINT `con_aitsu_article_property_ibfk_1` FOREIGN KEY (`propertyid`) REFERENCES `con_aitsu_property` (`propertyid`) ON DELETE CASCADE;
*/

class Aitsu_Ee_Article_Config {

	protected $idartlang;
	protected $modulId;
	protected $fields;
	protected $fieldIndex = -1;
	protected $config = array ();
	protected $configNotI;
	protected $editMode = false;
	protected $inherit;
	protected $fieldIndizes = array ();

	protected function __construct($idartlang, $inherit = false) {

		$this->idartlang = $idartlang;
		
		$this->editMode = Aitsu_Registry :: isEdit();
		
		$this->inherit = $inherit;

		$this->_readConfiguration();
		$this->_updateFromPost();

		if ($this->editMode) {
			echo $this->_getBrowserUpdater();
		}
	}

	protected function _updateFromPost() {

		if (!$this->editMode) {
			return;
		}

		if (!isset ($_POST['formProcessed'])) {
			return;
		}

		/*
		 * Unset the values of the processed modulId.
		 */
		unset ($this->config[$_POST['formProcessed']]);

		/*
		 * Reset the values.
		 */
		foreach ($_POST as $key => $value) {
			if (preg_match('/^(.*?)IDX(\\d{3})$/', $key, $match) == 1 && (!empty ($value[1]) || $value[1] == '0')) {
				if ($this->fields[$value[0]] == 'float') {
					$value[1] = str_replace(',', '.', $value[1]);
				}
				$this->config[$match[1]][$value[0]] = $value[1];
				$this->configNotI[$match[1]][$value[0]] = $value[1];
			}
		}

		Aitsu_Core_Article :: factory($this->idartlang)->touch();
	}

	protected function _setArticleProperties() {

		if (!isset ($_POST['formProcessed'])) {
			return;
		}

		$properties = array ();
		$results = Aitsu_Db :: fetchAll("" .
		"select * from _aitsu_property");
		if ($results) {
			foreach ($results as $result) {
				$properties[$result['identifier']] = array (
					'type' => $result['type'],
					'id' => $result['propertyid']
				);
			}
		}

		foreach ($this->fields as $field) {
			if (isset ($field['type'])) {
				
				if (!array_key_exists($this->modulId . ':' . $field['name'], $properties)) {
					/*
					 * Add entry in properties table.
					 */
					$insertId = Aitsu_Db :: query("" .
					"insert into _aitsu_property " .
					"(identifier, type) " .
					"values " .
					"(?, ?) ", array (
						$this->modulId . ':' . $field['name'],
						$field['type']
					))->getLastInsertId();
					$properties[$this->modulId . ':' . $field['name']] = array (
						'type' => $field['type'],
						'id' => $insertId
					);
				}
				
				$type = $field['type'] == 'serialized' ? 'text' : $field['type'];

				/*
				 * Add value to the properties table.
				 */
				Aitsu_Db :: query("" .
				"replace into _aitsu_article_property " .
				"(propertyid, idartlang, {$type}value) " .
				"values " .
				"(?, ?, ?) " .
				"", array (
					$properties[$this->modulId . ':' . $field['name']]['id'],
					$this->idartlang,
					$field['type'] == 'serialized' ? serialize($this->config[$this->modulId][$field['name']]) : $this->config[$this->modulId][$field['name']] 
				));
			}
		}
	}

	public static function getInstance($modulId, $inherit = false) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self(Aitsu_Registry :: get()->env->idartlang, $inherit);
		}

		$instance->modulId = $modulId;
		$instance->fields = null;
		$instance->inherit = $inherit;

		return $instance;
	}

	protected function _getFieldName() {

		$this->fieldIndex++;

		return $this->modulId . 'IDX' . str_pad($this->fieldIndex, 3, '0', STR_PAD_LEFT);
	}

	public function select($name, $keyValuePairs, $index = null, $type = 'text', $label = null) {

		if (!$this->editMode) {
			return $this;
		}

		$fieldName = $this->_getFieldName();

		$field = '<select name="' . $fieldName . '[1]" size="1">';
		foreach ($keyValuePairs as $key => $value) {
			if (isset ($this->config[$this->modulId][$name]) && $this->config[$this->modulId][$name] == $value) {
				$field .= '<option value="' . $value . '" selected="selected">' . $key . '</option>';
			} else {
				$field .= '<option value="' . $value . '">' . $key . '</option>';
			}
		}
		$field .= '</select>';

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'index' => $index,
			'type' => $type,
			'label' => $label,
			'renderType' => 'select'
		);

		return $this;
	}

	public function date($name, $label, $index = null) {

		if (!$this->editMode) {
			return $this;
		}

		$fieldName = $this->_getFieldName();
		$id = uniqid('datePicker');

		$field = '<input name="' . $fieldName . '[1]" type="text" id="' . $id . '" value="' . $this->config[$this->modulId][$name] . '">';
		$field .= '<script type="text/javascript">$(function() {$("#' . $id . '").datepicker({ dateFormat: \'yy-mm-dd\' });});</script>';

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'index' => $index,
			'type' => 'date',
			'label' => $label,
			'renderType' => 'text'
		);

		return $this;
	}

	public function text($name, $width = null, $index = null, $type = 'text', $label = null) {

		if (!$this->editMode) {
			return $this;
		}
		
		$fieldName = $this->_getFieldName();

		if ($width != null) {
			$field = '<input type="text" name="' . $fieldName . '[1]" value="' . $this->config[$this->modulId][$name] . '" style="width:' . $width . '%;" />';
		} else {
			$field = '<input type="text" name="' . $fieldName . '[1]" value="' . $this->config[$this->modulId][$name] . '" />';
		}

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'index' => $index,
			'type' => $type,
			'label' => $label,
			'renderType' => 'text'
		);

		return $this;
	}

	public function textarea($name, $width = null, $height = null, $index = null, $label = null) {

		if (!$this->editMode) {
			return $this;
		}

		$fieldName = $this->_getFieldName();

		if ($width != null && $height != null) {
			$field = '<textarea name="' . $fieldName . '[1]">' . htmlentities($this->config[$this->modulId][$name]) . '</textarea>';
		} else {
			$field = '<textarea name="' . $fieldName . '[1]" style="width:' . $width . '%; height:' . $height . '%;">' . htmlentities($this->config[$this->modulId][$name]) . '</textarea>';
		}

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'index' => $index,
			'type' => 'text',
			'label' => $label,
			'renderType' => 'textarea'
		);

		return $this;
	}

	public function checkbox($name, $keyValuePairs, $label = null) {

		if (!$this->editMode) {
			return $this;
		}

		$fieldName = $this->_getFieldName();

		$field = '<fieldset>';
		foreach ($keyValuePairs as $key => $value) {
			if (isset ($this->configNotI[$this->modulId][$name]) && in_array($value, $this->configNotI[$this->modulId][$name])) {
				$field .= '<input type="checkbox" name="' . $fieldName . '[1][]" value="' . $value . '" checked="checked" />&nbsp;' . $key . '<br />';
			} else {
				$field .= '<input type="checkbox" name="' . $fieldName . '[1][]" value="' . $value . '" />&nbsp;' . $key . '<br />';
			}
		}
		$field .= '</fieldset>';

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'type' => 'serialized',
			'label' => $label,
			'renderType' => 'checkbox'
		);

		return $this;
	}

	public function radio($name, $keyValuePairs, $index = null, $type = 'text', $label = null) {

		if (!$this->editMode) {
			return $this;
		}

		$fieldName = $this->_getFieldName();

		$field = '<fieldset>';
		foreach ($keyValuePairs as $key => $value) {
			if (isset ($this->configNotI[$this->modulId][$name]) && $this->configNotI[$this->modulId][$name] == $value) {
				$field .= '<input type="radio" name="' . $fieldName . '[1]" value="' . $value . '" checked="checked" />&nbsp;' . $key . '<br />';
			} else {
				$field .= '<input type="radio" name="' . $fieldName . '[1]" value="' . $value . '" />&nbsp;' . $key . '<br />';
			}
		}
		$field .= '</fieldset>';

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'index' => $index,
			'type' => $type,
			'label' => $label,
			'renderType' => 'radio'
		);

		return $this;
	}

	public function __get($name) {

		if (!isset ($this->config[$this->modulId][$name])) {
			return null;
		}

		if (strlen(trim($this->config[$this->modulId][$name])) == 0) {
			return null;
		}

		return $this->config[$this->modulId][$name];
	}
	
	public function getConfig() {
		
		if (!isset($this->config[$this->modulId])) {
			return;
		}
		
		return $this->config[$this->modulId];
	}

	public function file($name, $index = null, $label = null) {

		if (!$this->editMode) {
			return $this;
		}

		$fieldId = uniqid('field');
		$fieldName = $this->_getFieldName();

		// $field = '<input type="text" id="' . $fieldId . '" name="' . $fieldName . '[1]" value="' . $this->configNotI[$this->modulId][$name] . '" style="width: 250px;" />';
		// $field .= '&nbsp;<span style="font-weight:bold;cursor:pointer;" onclick="javascript:fb_handle = window.open(\'' . Aitsu_Registry :: get()->config->rootUrl . 'frameset.php?area=upl&contenido=' . $_GET['contenido'] . '&appendparameters=imagebrowser\', \'filebrowser\', \'dialog=yes,resizable=yes\'); fb_intervalhandle = window.setInterval(\'updateFilebrowser()\', 250); fb_currentfield = document.getElementById(\'' . $fieldId . '\');">BROWSE</span>';

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'index' => $index,
			'type' => 'text',
			'label' => $label
		);

		return $this;

	}

	public function link($name, $index = null, $label = null) {

		if (!$this->editMode) {
			return $this;
		}

		$fieldId = uniqid('field');
		$fieldName = $this->_getFieldName();

		// $field = '<input type="text" id="' . $fieldId . '" name="' . $fieldName . '[1]" value="' . $this->configNotI[$this->modulId][$name] . '" style="width: 250px;" />';
		// $field .= '&nbsp;<span style="font-weight:bold;cursor:pointer;" onclick="javascript:fb_handle = window.open(\'' . Aitsu_Registry :: get()->config->rootUrl . 'frameset.php?area=upl&contenido=' . $_GET['contenido'] . '&appendparameters=filebrowser\', \'filebrowser\', \'dialog=yes,resizable=yes\'); fb_intervalhandle = window.setInterval(\'updateFilebrowser()\', 250); fb_currentfield = document.getElementById(\'' . $fieldId . '\');">BROWSE</span>';

		$this->fields[] = array (
			'modulId' => $this->modulId,
			'name' => $name,
			'fieldName' => $fieldName,
			'html' => $field,
			'index' => $index,
			'type' => 'text',
			'label' => $label
		);

		return $this;

	}

	public function getOut() {

		if (!$this->editMode) {
			return '';
		}
		
		$this->_setArticleProperties();

		$formName = uniqid('form');
		$configAreaId = uniqid('area');

		$out = '<form class="aitsu_form" action="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" method="POST" name="' . $formName . '">';
		$out .= '<input type="hidden" name="formProcessed" value="' . $this->modulId . '" />';

		if ($this->useCmsText) {
			$out .= '<input type="hidden" name="useCmsText" value="1" />';
		}

		if ($this->useArticleProperties) {
			$out .= '<input type="hidden" name="useArticleProperties" value="1" />';
		}

		$out .= '<div class="aitsu_form_header clearfix"><h6>' . $this->modulId . '</h6><ul class="ui-widget ui-helper-clearfix right"><li class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-triangle-s" onclick="$(\'#' . $configAreaId . '\').toggle();$(this).toggleClass(\'ui-icon-circle-triangle-n\');">&nbsp;</span></li></ul></div>';
		$out .= '<div id="' . $configAreaId . '" style="display:none;" class="aitsu_form_content">';
		$out .= '<fieldset>';

		foreach ($this->fields as $field) {
			if ($field['renderType'] == 'radio' || $field['renderType'] == 'checkbox') {
				$out .= '<div class="type-check">';
			}
			elseif ($field['renderType'] == 'select') {
				$out .= '<div class="type-select">';
			} else {
				$out .= '<div class="type-text">';
			}
			if ($field['label'] == null) {
				$out .= '<label>' . $field['name'] . ':</label>';
			} else {
				$out .= '<label>' . $field['label'] . ':</label>';
			}
			$out .= $field['html'];
			$out .= '<input type="hidden" name="' . $field['fieldName'] . '[0]" value="' . $field['name'] . '" />';
			$out .= '</div>';
		}

		$out .= '</fieldset>';
		$out .= '<div class="type-button clearfix"><ul class="ui-widget ui-helper-clearfix right"><li class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-check" onclick="document.' . $formName . '.submit();"/></li></ul></div>';
		$out .= '</div>';
		$out .= '</form>';

		return $out;
	}

	protected function _getBrowserUpdater() {

		if (!$this->editMode) {
			return '';
		}

		$js =<<<JS
<script language="javascript" type="text/javascript">
var fb_handle;
var fb_intervalhandle;
var fb_currentfield;
function updateFilebrowser() {
	if (!fb_handle) {
		return;
	}
	if (!fb_handle.left) {
		return;
	}
	if (!fb_handle.left.left_top) {
		return;
	}
	if (!fb_handle.left.left_top.document.getElementById("selectedfile")) {
		return;
	}
	if (fb_handle.left.left_top.document.getElementById("selectedfile").value != "") {
		fb_currentfield.value = fb_handle.left.left_top.document.getElementById("selectedfile").value;
		fb_handle.close();
		window.clearInterval(fb_intervalhandle);
	}
}
</script>
JS;
		return $js;
	}

	protected function _readConfiguration() {

		$configs = Aitsu_Db :: fetchAll('' .
		'select STRAIGHT_JOIN distinct ' .
		'	partlang.idartlang, ' .
		'	ptype.identifier, ' .
		'	ptype.type, ' .
		'	prop.textvalue, ' .
		'	prop.floatvalue, ' .
		'	prop.datevalue, ' .
		'	if(artlang.idartlang = partlang.idartlang, 0, 1) as itype ' .
		'from _art_lang as artlang ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'left join _cat as child on catart.idcat = child.idcat ' .
		'left join _cat as node on child.lft between node.lft and node.rgt and child.idclient = node.idclient ' .
		'left join _cat_lang as catlang on node.idcat = catlang.idcat and catlang.idlang = artlang.idlang ' .
		'left join _art_lang as partlang on partlang.idartlang = catlang.startidartlang or partlang.idartlang = artlang.idartlang ' .
		'left join _aitsu_article_property prop on partlang.idartlang = prop.idartlang ' .
		'left join _aitsu_property as ptype on prop.propertyid = ptype.propertyid ' .
		'where ' .
		'	artlang.idartlang = ? ' .
		'	and partlang.idartlang is not null ' .
		'	and ptype.propertyid is not null ' .
		'order by ' .
		'	if(partlang.idartlang = artlang.idartlang, 1, 0) asc, ' .
		'	node.lft asc ', array (
			$this->idartlang
		));
		
		if (!$configs) {
			return;
		}
		
		foreach ($configs as $config) {
			$modulId = strtok($config['identifier'], ':');
			$name = strtok(':');
			$type = $config['type'] == 'serialized' ? 'text' : $config['type'];
			$value = $config[$type . 'value'];
			$this->configNotI[$modulId][$name] = $config['type'] == 'serialized' ? unserialize($value) : $value;
			if ($this->inherit || $config['itype'] == 0) {
				$this->config[$modulId][$name] = $config['type'] == 'serialized' ? unserialize($value) : $value;
			}
		}
	}
}
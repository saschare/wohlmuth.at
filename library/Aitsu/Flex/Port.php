<?php


/**
 * aitsu FlexPort.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Port.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Flex_Port {

	protected $db;

	protected function __construct() {
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function addArticle($idartlang, $template, $destination, $position = null, $from = null, $until = null) {

		$idflexportdest = $this->_getIdflexportdest($idartlang, $destination);

		if ($position != null && Aitsu_Db :: fetchOne("" .
			"select count(*) " .
			"from _aitsu_flexport_article " .
			"where " .
			"	idflexportdest = ? " .
			"	and position = ? " .
			"	and idartlang != ? ", array (
				$idflexportdest,
				$position,
				$idartlang
			)) > 0) {
			/*
			 * There is already an article at the specified position.
			 */
			Aitsu_Db :: query("" .
			"update _aitsu_flexport_article " .
			"set position = position + 1 " .
			"where " .
			"	idflexportdest = ? " .
			"	and position >= ? ", array (
				$idflexportdest,
				$position
			));
		}

		Aitsu_Db :: query("" .
		"replace into _aitsu_flexport_article " .
		"(idflexportdest, idartlang, template, start, end, position) " .
		"values " .
		"(?, ?, ?, ?, ?, ?) ", array (
			$idflexportdest,
			$idartlang,
			$template,
			$from,
			$until,
			$position
		));

		$this->_reorderPositions($idflexportdest);
	}

	public function removeArticle($idartlang, $destination) {

		$idflexportdest = $this->_getIdflexportdest($idartlang, $destination);

		Aitsu_Db :: query("" .
		"update " .
		"	_aitsu_flexport_article as a, " .
		"	_aitsu_flexport_article as b " .
		"set b.position = b.position - 1 " .
		"where " .
		"	a.idflexportdest = b.idflexportdest " .
		"	and a.idflexportdest = ? " .
		"	and a.idartlang = ? " .
		"	and b.position > a.position", array (
			$idflexportdest,
			$idartlang
		));

		Aitsu_Db :: query("" .
		"delete from _aitsu_flexport_article " .
		"where " .
		"	idflexportdest = ? " .
		"	and idartlang = ? ", array (
			$idflexportdest,
			$idartlang
		));

		$this->_reorderPositions($idflexportdest);
	}

	protected function _getIdflexportdest($idartlang, $destination) {

		if (is_a($destination, 'Aitsu_Flex_Port_Destination')) {
			return $destination->getIdflexportdest();
		}

		if (substr($destination, 0, strlen('id.')) == 'id.') {
			return substr($destination, 3);
		}

		return Aitsu_Db :: fetchOne("" .
		"select " .
		"	dest.idflexportdest " .
		"from _art_lang as artlang " .
		"left join _lang as lang on artlang.idlang = lang.idlang " .
		"left join _art_lang as artdestlang on lang.idlang = artdestlang.idlang " .
		"left join _aitsu_flexport_dest as dest on dest.idartlang = artdestlang.idartlang " .
		"where " .
		"	artlang.idartlang = ? " .
		"	and dest.name = ?", array (
			$idartlang,
			$destination
		));
	}

	protected function _getTemplatesFor($idflexportdest) {

		$destTypes = Aitsu_Db :: fetchCol("" .
		"select ttype.token " .
		"from _aitsu_flexport_desttype as dest " .
		"left join _aitsu_flexport_ttype as ttype on dest.idflexportttype = ttype.idflexportttype " .
		"where dest.idflexportdest = ?", array (
			$idflexportdest
		));

		foreach (glob(Aitsu_Registry :: get()->config->templatePath . "/FlexPort_*.html") as $filename) {
			$fileContent = file_get_contents($filename);
			if (preg_match('/^\\s*\\*\\s*FlexPortType:(.*)$/m', $fileContent, $match)) {
				if (in_array(trim($match[1]), $destTypes)) {
					preg_match('/FlexPort_(.*?)\\.html/', basename($filename), $templateName);
					$returnValue[] = $templateName[1];
				}
			}
		}

		return $returnValue;
	}

	protected function _getAvailablePortsFor($idartlang) {

		return Aitsu_Db :: fetchAll("" .
		"select " .
		"	dest.idflexportdest, " .
		"	dest.name, " .
		"	if (max(article.position) is null, 1, max(article.position) + 1) as maxpos " .
		"from _art_lang as artlang " .
		"left join _art_lang as artlangdest on artlang.idlang = artlangdest.idlang " .
		"left join _aitsu_flexport_dest as dest on artlangdest.idartlang = dest.idartlang " .
		"left join _aitsu_flexport_article as article on dest.idflexportdest = article.idflexportdest " .
		"where " .
		"	artlang.idartlang = ? " .
		"	and dest.idartlang is not null " .
		"group by " .
		"	dest.idflexportdest, " .
		"	dest.name " .
		"order by dest.name asc ", array (
			$idartlang
		));
	}

	protected function _getPublicationData($idartlang, $idflexportdest) {

		return Aitsu_Db :: fetchRow("" .
		"select " .
		"	template, " .
		"	start, " .
		"	end, " .
		"	position, " .
		"	date_format(start, '%Y-%m-%d') as startdate, " .
		"	date_format(end, '%Y-%m-%d') as enddate, " .
		"	date_format(start, '%H:%i') as starttime, " .
		"	date_format(end, '%H:%i') as endtime " .
		"from _aitsu_flexport_article " .
		"where " .
		"	idartlang = ? " .
		"	and idflexportdest = ? " .
		"limit 0, 1 ", array (
			$idartlang,
			$idflexportdest
		));
	}

	public function getEditForm($idartlang, $edit) {

		if (!$edit) {
			return '';
		}

		if (Aitsu_Db :: fetchOne("" .
			"select count(idflexportdest) " .
			"from _aitsu_flexport_dest " .
			"where idartlang = ? ", array (
				$idartlang
			)) > 0) {
			/*
			 * The current article is a FlexPort destination and may
			 * therefore not be part of a FlexPort.
			 */
			return '';
		}

		$this->_saveFormData($idartlang);

		$formName = uniqid('form');
		$out = '<form name="' . $formName . '" action="' . Aitsu_Util :: getCurrentUrl() . '" method="post" class="aitsu_form">';

		$ports = $this->_getAvailablePortsFor($idartlang);
		if (!$ports) {
			return '';
		}

		foreach ($ports as $port) {
			$data = $this->_getPublicationData($idartlang, $port['idflexportdest']);

			$out .= '<fieldset><legend>' . $port['name'] . '</legend><div class="subcolumns">';

			/*
			 * Select field with available templates.
			 */
			$out .= '<div class="c25l"><div class="subcl type-select">' .
			'<label for="template">Template</label>' .
			'<select name="aitsuFlexPortTemplate[' . $port['idflexportdest'] . ']" size="1" style="width:98%;">';

			$availableTemplates = $this->_getTemplatesFor($port['idflexportdest']);
			$out .= '<option value="">--</option>';
			if ($availableTemplates) {
				foreach ($availableTemplates as $template) {
					$selected = $data['template'] == $template ? ' selected="selected"' : '';
					$out .= ' <option value="' . $template . '"' . $selected . '>' . $template . '</option>';
				}
			}

			$out .= '</select></div></div>';

			/*
			 * Publish from field.
			 */
			$id = uniqid();
			$out .= '<div class="c25l"><div class="subcl type-text">' .
			'<label for="start">online from</label>' .
			'<input id="' . $id . '" name="aitsuFlexPortStart[' . $port['idflexportdest'] . ']" value="' . $data['startdate'] . '" size="20" type="text" style="width:98%;">' .
			'<input name="aitsuFlexPortStartTime[' . $port['idflexportdest'] . ']" value="' . $data['starttime'] . '" size="20" type="text" style="width:98%; margin-top:3px;">' .
			'</div></div>';
			$out .= '<script type="text/javascript">$(function() {$("#' . $id . '").datepicker({ dateFormat: \'yy-mm-dd\' });});</script>';

			/*
			 * Publish until field.
			 */
			$id = uniqid();
			$out .= '<div class="c25l"><div class="subcl type-text">' .
			'<label for="start">until</label>' .
			'<input id="' . $id . '" name="aitsuFlexPortEnd[' . $port['idflexportdest'] . ']" value="' . $data['enddate'] . '" size="20" type="text" style="width:98%;">' .
			'<input name="aitsuFlexPortEndTime[' . $port['idflexportdest'] . ']" value="' . $data['endtime'] . '" size="20" type="text" style="width:98%; margin-top:3px;">' .
			'</div></div>';
			$out .= '<script type="text/javascript">$(function() {$("#' . $id . '").datepicker({ dateFormat: \'yy-mm-dd\' });});</script>';

			/*
			 * Select field with available positions.
			 */
			$out .= '<div class="c25l"><div class="subcl type-select">' .
			'<label for="template">Position</label>' .
			'<select name="aitsuFlexPortPosition[' . $port['idflexportdest'] . ']" size="1" style="width:98%;">';

			for ($i = 1; $i <= $port['maxpos']; $i++) {
				$selected = $data['position'] == $i ? ' selected="selected"' : '';
				$out .= ' <option value="' . $i . '"' . $selected . '>' . $i . '</option>';
			}

			$out .= '</select></div></div>';

			$out .= '</div></fieldset>';
		}

		$out .= '<div class="type-button clearfix">' .
		'<ul class="ui-widget ui-helper-clearfix right">' .
		'<li class="ui-state-default ui-corner-all">' .
		'<span class="ui-icon ui-icon-circle-check" onclick="document.' . $formName . '.submit();"/>' .
		'</li></ul></div>';

		$out .= '</form>';

		return $out;
	}

	protected function _reorderPositions($idflexportdest) {

		Aitsu_Db :: query("set @a := 0");

		Aitsu_Db :: query("" .
		"update _aitsu_flexport_article " .
		"set position = @a:= @a + 1 " .
		"where " .
		"	idflexportdest = ? " .
		"order by " .
		"	position ", array (
			$idflexportdest
		));
	}

	public function moveSourceInFrontOfTarget($sourceIdartlang, $targetIdartlang, $idflexportdest) {

		Aitsu_Db :: query("" .
		"select position into @position from _aitsu_flexport_article " .
		"where " .
		"	idflexportdest = ? " .
		"	and idartlang = ? ", array (
			$idflexportdest,
			$targetIdartlang
		));

		Aitsu_Db :: query("" .
		"update _aitsu_flexport_article " .
		"set position = position + 1 " .
		"where " .
		"	idflexportdest = ? " .
		"	and position >= @position ", array (
			$idflexportdest
		));

		Aitsu_Db("" .
		"update _aitsu_flexport_article " .
		"set position = @position " .
		"where " .
		"	idflexportdest = ? " .
		"	and idartlang = ? ", array (
			$idflexportdest,
			$sourceIdartlang
		));

		$this->_reorderPositions($idflexportdest);

		Aitsu_Core_Article :: factory($sourceIdartlang)->touch();
	}

	protected function _saveFormData($idartlang) {

		if (!isset ($_POST['aitsuFlexPortTemplate'])) {
			return;
		}

		foreach ($_POST['aitsuFlexPortTemplate'] as $idflexportdest => $template) {
			if ($template == '') {
				$this->removeArticle($idartlang, 'id.' . $idflexportdest);
			} else {
				$startDate = preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}$/', $_POST['aitsuFlexPortStart'][$idflexportdest]) ? $_POST['aitsuFlexPortStart'][$idflexportdest] : '0000-00-00';
				$endDate = preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}$/', $_POST['aitsuFlexPortEnd'][$idflexportdest]) ? $_POST['aitsuFlexPortEnd'][$idflexportdest] : '0000-00-00';

				$startTime = preg_match('/^\\d{2}\\:\\d{2}$/', $_POST['aitsuFlexPortStartTime'][$idflexportdest]) ? $_POST['aitsuFlexPortStartTime'][$idflexportdest] : '00:00';
				$endTime = preg_match('/^\\d{2}\\:\\d{2}$/', $_POST['aitsuFlexPortEndTime'][$idflexportdest]) ? $_POST['aitsuFlexPortEndTime'][$idflexportdest] : '23:59';

				$start = $startDate . ' ' . $startTime;
				$end = $endDate . ' ' . $endTime;

				try {
					$this->addArticle($idartlang, $template, 'id.' . $idflexportdest, $_POST['aitsuFlexPortPosition'][$idflexportdest], $start, $end);
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}
		}

		Aitsu_Core_Article :: factory($idartlang)->touch();
	}
}
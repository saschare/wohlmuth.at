<?php


/**
 * aitsu FlexPort destination.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Destination.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Flex_Port_Destination {

	protected $db;
	protected $name;
	protected $idartlang;
	protected $idflexportdest;
	protected $templateType;

	protected function __construct($idartlang, $destinationName) {

		$this->name = $destinationName;

		$this->idartlang = $idartlang;

		$this->_setIdflexportdest();
	}

	public static function getInstance($idartlang, $destinationName = null) {

		static $instance;

		if (!isset ($instance)) {
			if ($destinationName == null) {
				throw new Aitsu_Flex_Port_Exception_DestinationNameMissing('' .
				'The very first call of the getInstance method within ' .
				'a given request has to indicate the destination name.' .
				'');
			}
			$instance = new self($idartlang, $destinationName);
		}

		return $instance;
	}

	protected function _setIdflexportdest() {

		$this->idflexportdest = Aitsu_Db :: fetchOne("" .
		"select idflexportdest from _aitsu_flexport_dest " .
		"where idartlang = ? and name = ? ", array (
			$this->idartlang,
			$this->name
		));

		if ($this->idflexportdest) {
			return;
		}

		$this->idflexportdest = Aitsu_Db :: fetchOne("" .
		"select idflexportdest from _aitsu_flexport_dest " .
		"where idartlang = ? ", array (
			$this->idartlang
		));

		if ($this->idflexportdest) {
			Aitsu_Db :: query("" .
			"update _aitsu_flexport_dest " .
			"set name = ? " .
			"where idartlang = ? ", array (
				$this->name,
				$this->idartlang
			));
			return;
		}

		Aitsu_Db :: query("" .
		"insert into _aitsu_flexport_dest " .
		"(idartlang, name) " .
		"values " .
		"(?, ?) ", array (
			$this->idartlang,
			$this->name
		));

		$this->idartlang = $this->db->lastInsertId();
	}

	public function addTemplateTypes($tokens, $edit) {

		if (!$edit) {
			return;
		}

		$this->_removeUnusedDestinations();

		$tokens = explode(',', $tokens);

		Aitsu_Db :: query("" .
		"delete from _aitsu_flexport_desttype " .
		"where idflexportdest = ? ", $this->idflexportdest);

		foreach ($tokens as $token) {

			$token = trim($token);

			$this->templateType[] = $token;

			$tokenId = Aitsu_Db :: fetchOne("" .
			"select idflexportttype from _aitsu_flexport_ttype " .
			"where token = ? ", array (
				$token
			));

			if (!$tokenId) {
				Aitsu_Db :: query("" .
				"insert into _aitsu_flexport_ttype " .
				"(token) " .
				"values " .
				"(?) ", array (
					$token
				));
				$tokenId = $this->db->lastInsertId();
			}

			Aitsu_Db :: query("" .
			"insert into _aitsu_flexport_desttype " .
			"(idflexportdest, idflexportttype) " .
			"values " .
			"(?, ?) ", array (
				$this->idflexportdest,
				$tokenId
			));
		}

		return $this;
	}

	public function getIdflexportdest() {

		return $this->idflexportdest;
	}

	public function addArticle($idartlang, $template, $position, $from = null, $until = null) {

		Aitsu_Flex_Port :: getInstance()->addArticle($idartlang, $template, $this, $position, $from, $until);
	}

	public function removeArticle($idartlang) {

		Aitsu_Flex_Port :: getInstance()->removeArticle($idartlang, $this);
	}

	public function getOut($edit = false) {

		if ($edit) {
			/*
			 * To enable drag 'n drop we include some JavaScript.
			 */
			$flexFormId = uniqid();
			$destId = uniqid();
			$sourceId = uniqid();
			echo '<script type="text/javascript">' .
			'$(document).ready(function(){' .
			'	$(".flexPortDraggable").draggable({ revert: true, cursor: \'pointer\', opacity: 0.55, stack: { group: \'*\' }, helper: \'clone\', cursorAt: { left: 20, top: 20 }, appendTo: \'parent\' });' .
			'	$(".flexPortDraggable").droppable({' .
			'		hoverClass: \'ui-drophover\',' .
			'		drop: function(event, ui) { ' .
			'			$("#' . $destId . '").attr("value", $(this).attr(\'id\'));' .
			'			$("#' . $sourceId . '").attr("value", ui.draggable.attr(\'id\'));' .
			'			$("#' . $flexFormId . '").submit();' .
			'	 	} ' .
			'	});' .
			'});' .
			'</script>' .
			'<form id="' . $flexFormId . '" method="post" action="' . Aitsu_Util :: getCurrentUrl() . '" style="display:inline;">' .
			'<input type="hidden" id="' . $sourceId . '" name="flexPortSource" value="" />' .
			'<input type="hidden" id="' . $destId . '" name="flexPortTarget" value="" />' .
			'<input type="hidden" name="flexPortDest" value="' . $this->idflexportdest . '" />' .
			'</form>';

			$this->_updatePositions();

			/*
			 * No caching is done. We deliver a newly built output.
			 */
			return $this->_buildOutput($edit);
		}

		/*
		 * Cache is reduced to maximum 15 minutes by default for FlexPort destinations.
		 */
		Aitsu_Cache_Page :: lifetime(15 * 60);

		/*
		 * Then the persistence has to be loaded.
		 */
		$p = Aitsu_Persistence :: getInstance('FlexPortDestination', $this->idflexportdest);
		if ($p->lastBuilt == null || $this->_haveChangesBeenMadeSince($p->lastBuilt)) {
			/*
			 * No build has been made or there have been changes in content
			 * since the last build. Therefore the output is rebuilt.
			 */
			$p->output = $this->_buildOutput($edit);

			/*
			 * The time of the build is taken from the database to prevent
			 * problems occuring due to different times or time zones on 
			 * web and database server.
			 */
			$p->lastBuilt = Aitsu_Db :: fetchOne("select now()");

			$p->save(0);
		}

		return $p->output;
	}

	protected function _haveChangesBeenMadeSince($date) {

		if (Aitsu_Db :: fetchOne("" .
			"select " .
			"	count(article.idartlang) as anzahl " .
			"from _aitsu_flexport_article as article " .
			"left join _art_lang as artlang on article.idartlang = artlang.idartlang " .
			"where " .
			"	artlang.lastmodified > ? " .
			"	and article.idflexportdest = ? ", array (
				$date,
				$this->idflexportdest
			)) > 0) {
			/*
			 * One or more articles have been changed since the last build.
			 */
			return true;
		}

		if (Aitsu_Db :: fetchOne("" .
			"select " .
			"	count(idartlang) " .
			"from _aitsu_flexport_article " .
			"where " .
			"	(" .
			"		start between ? and now() " .
			"		or end between ? and now() " .
			"	)" .
			"	and idflexportdest = ?", array (
				$date,
				$date,
				$this->idflexportdest
			))) {
			/*
			 * At least one's article publish start or end date is between
			 * the time of the last build an now.
			 */
			return true;
		}

		return false;
	}

	protected function _buildOutput($edit) {

		$out = array ();

		/*
		 * Fetch the article's idartlang and the template to be used of the
		 * articles to be displayed according to their publish from and 
		 * publish until data for the current FlexPort destination.
		 */
		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	idartlang, " .
		"	template " .
		"from _aitsu_flexport_article " .
		"where " .
		"	idflexportdest = ? " .
		"	and start < now() " .
		"	and end > now() " .
		"order by " .
		"	position asc", array (
			$this->idflexportdest
		));

		if (!$results) {
			return '';
		}

		foreach ($results as $result) {
			/*
			 * Excecute the template with the data for the fetched idartlang.
			 */
			try {
				$tmp = $this->_executeTemplate($result['idartlang'], $result['template'], $edit);
				if ($edit) {
					$tmp = '<div class="flexPortDraggable ui-droppable" id="flexPort_' . $result['idartlang'] . '">' . $tmp . '</div>';
				}
				$out[] = array (
					'template' => $result['template'],
					'output' => $tmp,
					'idart' => Aitsu_Core_Article :: factory($result['idartlang'])->get('artlang/idart')
				);
			} catch (Aitsu_Flex_Port_Exception_RenderingNotPossible $e) {
				if ($edit) {
					$out[] = array (
						'template' => $result['template'],
						'output' => $e->getMessage(),
						'idart' => Aitsu_Core_Article :: factory($result['idartlang'])->get('artlang/idart')
					);
				}
			}
		}

		return $out;
	}

	protected function _executeTemplate($idartlang, $template) {

		$out = '';

		$template = file_get_contents(Aitsu_Registry :: get()->config->templatePath . "/FlexPort_$template.html");
		$article = Aitsu_Core_Article :: factory($idartlang);

		ob_start();
		eval ("?>\n" . $template);
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	protected function _removeUnusedDestinations() {

		Aitsu_Db :: query("" .
		"delete from _aitsu_flexport_dest " .
		"where idartlang not in (" .
		"	select idartlang from _art_lang " .
		"	) ");
	}

	protected function _updatePositions() {

		if (!isset ($_POST['flexPortSource']) || !isset ($_POST['flexPortTarget']) || !isset ($_POST['flexPortDest'])) {
			return;
		}
		
		preg_match('/\\d*$/', $_POST['flexPortSource'], $source);
		preg_match('/\\d*$/', $_POST['flexPortTarget'], $target);
		
		Aitsu_Flex_Port :: getInstance()->moveSourceInFrontOfTarget($source[0], $target[0], $_POST['flexPortDest']);
	}
}
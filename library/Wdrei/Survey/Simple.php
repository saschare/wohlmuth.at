<?php


/*
 * Development in progress. Do not use in production environments.
 * 2011-09-23 / A. Kummer
 */

/**
 * Wdrei_Survey_Simple erlaubt die Ausgabe sowie die Auswertung von Online-Auswertungen auf Basis
 * eines XML-Dokumentes, welches sowohl die Fragen und möglichen Antworten als auch die Auswertungs-
 * regeln enthält.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Wdrei_Survey_Simple {

	var $surveyXML, $formNamePrefix, $pageContent, $outputText, $analysisDone, $surveyProlog;

	function atqSimpleSurvey($surveyXML, $surveyProlog, $formNamePrefix, $outputText) {

		global $cfgClient, $client, $cfg;

		$this->pageContent = '';

		$this->outputText = $outputText;

		$this->analysisDone = false;

		$this->surveyProlog = $surveyProlog;
		$this->surveyXML = simplexml_load_file($cfgClient[$client]["path"]["frontend"] . '/' . $surveyXML);

		$this->formNamePrefix = $formNamePrefix;

		$isComplete = true;
		$currentBlock = ($this->getPost('showBlock') == null) ? (0) : ($this->getPost('showBlock'));
		if (!$this->isComplete($currentBlock -1)) {
			$currentBlock--;
			$isComplete = false;
		}

		if ($this->getPost('showBlock') == null) {
			/*
			 * Umfrageeinleitung ist einzublenden.
			 */
			$this->pageContent .= $this->renderProlog();
		}
		elseif ($currentBlock >= count($this->surveyXML->questions[0])) {
			/*
			 * Umfrage vollständig ausgefüllt. Auswertung ausgeben.
			 */
			$this->analysisDone = true;
			$this->pageContent .= $this->renderAnalysis();
		} else {
			/*
			 * Umfrage noch nich vollständig durchlaufen
			 */
			$this->pageContent .= $this->renderSurvey($currentBlock, !$isComplete);
		}
	}

	function renderProlog() {

		$returnValue = '<form action="' . $this->getAction() . '" method="POST" style="display:inline;">' . "\n";

		$returnValue .= '<div class="surveyProlog">' . $this->surveyProlog . '</div>';
		$returnValue .= '<div class="surveyButton"><button type="submit" name="' . $this->formNamePrefix . 'showBlock" value="0">' . $this->outputText['startAnalysis'] . '</button></div>' . "\n";

		$returnValue .= '</form>';

		return $returnValue;
	}

	function getAction() {

		global $idart, $idcat, $idartlang;

		$returnValue = 'front_content.php?';

		foreach ($_GET as $key => $value) {
			if ($key == 'idart' || $key == 'idcat' || $key == 'idartlang') {
				$value = $$key;
			}
			$returnValue .= $key . '=' . $value . '&';
		}

		return $returnValue;
	}

	function getPost($fieldName) {

		if (!isset ($_POST[$this->formNamePrefix . $fieldName])) {
			return null;
		}

		return $_POST[$this->formNamePrefix . $fieldName];
	}

	function renderSurvey($showBlock, $isRerendered = false) {

		$questionNo = 0;

		$returnValue = '<form action="' . $this->getAction() . '" method="POST" style="display:inline;">' . "\n";

		if ($isRerendered) {
			/*
			 * Kommentar ausgeben, dass die grau hinterlegten Felder auszufüllen sind.
			 */
			$returnValue .= '<div class="surveyNotCompleteWarning">' . $this->outputText['notCompleteWarning'] . '</div>';
		}

		$blockNo = -1;
		foreach ($this->surveyXML->questions[0] as $block) {

			$blockNo++;

			if ($blockNo == $showBlock) {
				/*
				 * Aktueller Frageblock wird angezeigt.
				 */

				if (isset ($block->blockcomment)) {
					/*
					 * Fragenblockkommentar ausgeben
					 */
					$returnValue .= '<div class="surveyBlockComment">' . utf8_decode($block->blockcomment[0]) . '</div>' . "\n";
				}

				foreach ($block->question as $question) {

					$questionNo++;

					$returnValue .= '<div class="surveyQuestionBlock">';

					if ($isRerendered && $question['required'] == 'true' && $this->getPost($questionNo) == null) {
						$requiredClass = ' surveyQuestionRequired';
					} else {
						$requiredClass = ' surveyQuestionNotRequired';
					}

					$reference = '';
					if (!empty ($question['reference']) && !empty ($question['referenceName'])) {
						$reference = '&nbsp;&nbsp;<a href="' . utf8_decode($question['reference']) . '" target="_blank" onclick="window.open(this.href, \'' . utf8_decode($question['referenceName']) . '\', \'width=500,height=400,resizable=yes,scrollbars=yes,screenX=200,screenY=100\'); return false;">[ ' . utf8_decode($question['referenceName']) . ' ]</a>';
					}

					$returnValue .= '<div class="surveyQuestionAnswer' . $requiredClass . '">' . "\n";
					$returnValue .= '<div class="surveyQuestion">' . $questionNo . '. ' . utf8_decode($question['question']) . $reference . '</div>' . "\n";

					if ($question['multiple'] == 'true') {
						/*
						 * Ausgabe von Checkboxen
						 */
						$returnValue .= $this->showAnwsersAsCheckboxes($question, $questionNo);
					} else {
						/*
						 * Ausgabe als Radiobuttons
						 */
						$returnValue .= $this->showAnswersAsRadiobuttons($question, $questionNo);
					}

					$returnValue .= '</div></div>' . "\n";
				}
			} else {
				/*
				 * Der aktuelle Fragenblock ist nicht anzuzeigen. Stattdessen sind die Feldwerte (sofern vorhanden)
				 * als HiddenFields auszugeben.
				 */
				for ($i = 0; $i < count($block->question); $i++) {
					$questionNo++;
					$post = $this->getPost($questionNo);
					if ($post != null) {
						if (is_array($post)) {
							/*
							 * Array ausgeben
							 */
							foreach ($post as $key => $value) {
								$returnValue .= '<input type="hidden" name="' . $this->formNamePrefix . $questionNo . '[' . $key . ']" value="' . $value . '" />' . "\n";
							}
						} else {
							/*
							 * Einzelwert ausgeben
							 */
							$returnValue .= '<input type="hidden" name="' . $this->formNamePrefix . $questionNo . '" value="' . $post . '" />' . "\n";
						}
					}
				}
			}
		}

		$returnValue .= '<div class="surveyButton">';

		$returnValue .= '<input type="hidden" name="' . $this->formNamePrefix . 'showBlock" id="' . $this->formNamePrefix . 'showBlock" value="0" />';

		if ($showBlock > 0) {
			/*
			 * Zurück-Knopf anzeigen
			 */
			$previousBlock = $showBlock -1;
			$returnValue .= '<button type="submit" value="' . $previousBlock . '" onClick="document.getElementById(\'' . $this->formNamePrefix . 'showBlock\').value=' . $previousBlock . '">' . $this->outputText['previous'] . '</button>&nbsp;' . "\n";
		}

		$nextBlock = $showBlock +1;
		if ($showBlock >= count($this->surveyXML->questions[0]) - 1) {
			/*
			 * Auswertungsknopf anzeigen
			 */
			$returnValue .= '<button type="submit" value="' . $nextBlock . '" onClick="document.getElementById(\'' . $this->formNamePrefix . 'showBlock\').value=' . $nextBlock . '">' . $this->outputText['doAnalysis'] . '</button>&nbsp;' . "\n";
		} else {
			/*
			 * Weiterknopf anzeigen
			 */
			$returnValue .= '<button type="submit" value="' . $nextBlock . '" onClick="document.getElementById(\'' . $this->formNamePrefix . 'showBlock\').value=' . $nextBlock . '">' . $this->outputText['next'] . '</button>&nbsp;' . "\n";
		}

		$returnValue .= '</div></form>' . "\n";
		return $returnValue;
	}

	function showAnwsersAsCheckboxes($question, $number) {

		$returnValue = '';

		$answerNo = -1;
		$post = $this->getPost($number);

		foreach ($question->answer as $answer) {
			$answerNo++;

			$returnValue .= '<div class="surveyAnswer">' . "\n";

			if ($post[$answerNo] == $answer['value']) {
				$returnValue .= '<div class="surveySelectButton"><input type="checkbox" checked="checked" name="' . $this->formNamePrefix . $number . '[' . $answerNo . ']" value="' . $answer['value'] . '"></div><div class="surveySelectButtonLabel">' . utf8_decode($answer['answer']) . '</div>' . "\n";
			} else {
				$returnValue .= '<div class="surveySelectButton"><input type="checkbox" name="' . $this->formNamePrefix . $number . '[' . $answerNo . ']" value="' . $answer['value'] . '"></div><div class="surveySelectButtonLabel">' . utf8_decode($answer['answer']) . '</div>' . "\n";
			}

			$returnValue .= '</div>' . "\n";
		}

		return $returnValue;
	}

	function showAnswersAsRadiobuttons($question, $number) {

		$returnValue = '';

		$post = $this->getPost($number);

		foreach ($question->answer as $answer) {

			$returnValue .= '<div class="surveyAnswer">' . "\n";

			if ($post == $answer['value']) {
				$returnValue .= '<div class="surveySelectButton"><input type="radio" checked="checked" name="' . $this->formNamePrefix . $number . '" value="' . $answer['value'] . '"></div><div class="surveySelectButtonLabel">' . utf8_decode($answer['answer']) . '</div>' . "\n";
			} else {
				$returnValue .= '<div class="surveySelectButton"><input type="radio" name="' . $this->formNamePrefix . $number . '" value="' . $answer['value'] . '"></div><div class="surveySelectButtonLabel">' . utf8_decode($answer['answer']) . '</div>' . "\n";
			}

			$returnValue .= '</div>' . "\n";
		}

		return $returnValue;
	}

	function getPageContent() {

		return $this->pageContent;
	}

	function isComplete($blockNo) {

		if ($blockNo < 0) {
			return true;
		}

		$questionNo = 0;
		for ($i = 0; $i < $blockNo; $i++) {
			$questionNo += count($this->surveyXML->questions[0]->block[$i]);
		}

		foreach ($this->surveyXML->questions[0]->block[$blockNo] as $question) {
			$questionNo++;

			if ($question['required'] == 'true') {
				if ($this->getPost($questionNo) == null) {
					return false;
				}
			}
		}

		return true;
	}

	function isAnalysisDone() {

		return $this->analysisDone;
	}

	function renderAnalysis() {

		$returnValue = '';

		/*
		 * Zunächst ist die Summe des Produkte von Wert und Gewicht sowie die gewichtete Mittelwert
		 * der Antworten zu ermitteln.
		 */
		$produkteSumme = 0;
		$gewichteSumme = 0;
		$questionNo = 0;

		foreach ($this->surveyXML->questions[0] as $block) {
			foreach ($block->question as $question) {
				$questionNo++;
				$post = $this->getPost($questionNo);

				/*
				 * Fragendurchschnitt ermitteln
				 */
				if ($post != null) {
					if (is_array($post)) {
						$questionMean = 0;
						foreach ($post as $value) {
							$questionMean += (double) $value;
						}
						$questionMean = $questionMean / count($post);
					} else {
						$questionMean = (double) $post;
					}
				}

				/*
				 * Produktsumme sowie Gewicht um entsprechende Werte in der Frage erhöhen.
				 */
				$gewichteSumme += (double) $question['weight'];
				$produkteSumme += ((double) $question['weight'] * $questionMean);
			}
		}

		$gewichtetesMittel = $produkteSumme / $gewichteSumme;

		$valueToBeUsed = ($this->surveyXML->analysis[0]['usemean'] == 'true') ? ($gewichtetesMittel) : ($produkteSumme);
		foreach ($this->surveyXML->analysis[0] as $score) {
			if ($valueToBeUsed >= $score['min'] && $valueToBeUsed < $score['max']) {
				$returnValue .= '<div class="surveyAnalysisTitle">' . htmlentities(utf8_decode($score['title'])) . '</div>';
				$returnValue .= '<div class="surveyAnalysisText">' . htmlentities(utf8_decode($score)) . '</div>';
				return $returnValue;
			}
		}

		return '';
	}

	function populateMask($content, $mask) {

		preg_match_all('/{([a-zA-Z]*)}/', $mask, $match);

		foreach ($match[1] as $placeholder) {
			$mask = str_replace('{' . $placeholder . '}', (isset ($content[$placeholder])) ? ($content[$placeholder]) : (''), $mask);
		}

		return $mask;
	}

	function debug($domain, $line, $message, & $variable, $dump = false) {

		if (!isset ($this->debug) || !in_array($domain, $this->debug)) {
			return;
		}

		$message = htmlentities($message);

		echo '<div style="border:1px solid black; padding:5px; width:100%; margin-top:5px; margin-bottom:5px;"><div>Line ' . $line . '</div>';
		echo '<div>' . $message . '</div>';

		if ($dump) {
			echo '<pre>';
			var_dump($variable);
			echo '</pre>';
		}

		echo '</div>';
	}

}
?>

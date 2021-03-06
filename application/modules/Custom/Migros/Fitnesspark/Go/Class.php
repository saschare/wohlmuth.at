<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Custom_Migros_Fitnesspark_Go_Class extends Aitsu_Module_Abstract {

	const FORM_NAME_PREFIX = 'Migros_FP_Go_';

	protected $_pageContent = '';
	protected $_analysisDone = false;
	protected $_surveyXML = null;

	protected function _init() {

		$view = $this->_getView();
		$view->formNamePrefix = self :: FORM_NAME_PREFIX;

		$dataFile = Aitsu_Content_Config_Media :: set($this->_index, 'Migros.Fitnesspark.Go.Xml', Aitsu_Translate :: translate('Go-Data'));
		$view->prolog = Aitsu_Content_Html :: get('Prolog');

		if (empty ($dataFile)) {
			return '';
		}

		$mediaId = Aitsu_Db :: fetchOne('' .
		'select mediaid from _media ' .
		'where ' .
		'	filename = :filename ' .
		'	and deleted is null ' .
		'order by ' .
		'	mediaid desc ' .
		'limit 0, 1', array (
			':filename' => $dataFile[0]
		));

		$this->_surveyXML = simplexml_load_file(APPLICATION_PATH . '/data/media/' . Aitsu_Registry :: get()->env->idart . '/' . $mediaId . '.xml');

		$isComplete = true;
		$currentBlock = ($this->_getPost('showBlock') == null) ? (0) : ($this->_getPost('showBlock'));
		if (!$this->_isComplete($currentBlock -1)) {
			$currentBlock--;
			$isComplete = false;
		}

		if ($this->_getPost('showBlock') == null) {
			/*
			 * Umfrageeinleitung ist einzublenden.
			 */
			return $view->render('prolog.phtml');
		}
		elseif ($currentBlock >= count($this->_surveyXML->questions[0])) {
			/*
			 * Umfrage vollständig ausgefüllt. Auswertung ausgeben.
			 */
			$this->analysisDone = true;
			
			/*
			* Zunächst ist die Summe des Produkte von Wert und Gewicht sowie die gewichtete Mittelwert
			* der Antworten zu ermitteln.
			*/
			$produkteSumme = 0;
			$gewichteSumme = 0;
			$questionNo = 0;

			foreach ($this->_surveyXML->questions[0] as $block) {
				foreach ($block->question as $question) {
					$questionNo++;
					$post = self :: _getPost($questionNo);

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

			$valueToBeUsed = ($this->_surveyXML->analysis[0]['usemean'] == 'true') ? ($gewichtetesMittel) : ($produkteSumme);
			
			$view->valueToBeUsed = $valueToBeUsed;
			$view->surveyXML = $this->_surveyXML;
			return $view->render('analysis.phtml');
		} else {
			/*
			 * Umfrage noch nich vollständig durchlaufen
			 */
			$view->showBlock = $currentBlock;
			$view->isRendered = !$isComplete;
			$view->surveyXML = $this->_surveyXML;
			return $view->render('survey.phtml');
		}

	}

	public static function _getPost($fieldName) {

		if (!isset ($_POST[self :: FORM_NAME_PREFIX . $fieldName])) {
			return null;
		}

		return $_POST[self :: FORM_NAME_PREFIX . $fieldName];
	}

	protected function _isComplete($blockNo) {

		if ($blockNo < 0) {
			return true;
		}

		$questionNo = 0;
		for ($i = 0; $i < $blockNo; $i++) {
			$questionNo += count($this->_surveyXML->questions[0]->block[$i]);
		}

		foreach ($this->_surveyXML->questions[0]->block[$blockNo] as $question) {
			$questionNo++;

			if ($question['required'] == 'true') {
				if (self :: _getPost($questionNo) == null) {
					return false;
				}
			}
		}

		return true;
	}

}
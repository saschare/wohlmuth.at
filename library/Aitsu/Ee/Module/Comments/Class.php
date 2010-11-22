<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 18277 2010-08-23 11:37:19Z akm $}
 */

class Aitsu_Ee_Module_Comments_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Article comments',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Allows to add comments to the current article (frontend).'),
			'type' => 'Interaction',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '0.1.0',
			'status' => 'beta',
			'url' => null,
			'id' => 'a0725362-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$instance = new self();
		Aitsu_Content_Edit :: noEdit('Comments', true);

		$instance->_processForm();

		Aitsu_Util_Javascript :: add('$("#comment-submit").live(\'click\', function() {$.post("' . Aitsu_Util :: getCurrentUrl() . '?clearcache", $("#comment-form").serialize(), function(data){$("#comments").html(data)}, "html"); return false;});');

		$output = '';
		if ($instance->_get('Comments', $output)) {
			return $output;
		}

		$view = $instance->_getView();
		$view->comments = array_reverse(Aitsu_Persistence_Comment :: getByIdartlang(Aitsu_Registry :: get()->env->idartlang));

		$output = $view->render('index.phtml');

		$instance->_save($output, 'eternal');

		return $output;
	}

	protected function _processForm() {

		$form = Aitsu_Form_Validation :: factory('comment');
		$form->setValidator('name', 'NoTags', array (
			'maxlength' => 255
		), true);
		$form->setValidator('email', 'Email', null, false);
		$form->setValidator('comment', 'NoTags', array (
			'maxlength' => 4000
		), true);

		try {
			$form->process(Aitsu_Persistence_Comment :: factory());
		} catch (Exception $e) {
			/*
			 * An exception may occur, if a visitor with the same ip
			 * sends the same comment within a delay of not more
			 * than 5 minutes.
			 */
		}

		if (!isset ($_POST['comment-ajax']) || $_POST['comment-ajax'] != '1') {
			return;
		}

		ob_end_clean();

		$view = $this->_getView();
		$view->comments = array_reverse(Aitsu_Persistence_Comment :: getByIdartlang(Aitsu_Registry :: get()->env->idartlang));

		echo $view->render('index.phtml');

		$this->_remove('Comments');

		exit;
	}
}
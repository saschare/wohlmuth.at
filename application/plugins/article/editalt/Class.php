<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 *
 * {@id $Id: Class.php 19916 2010-11-17 12:40:58Z akm $}
 */

class EditaltArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cd3092a-7de8-47cc-b390-10ec7f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'editalt',
			'tabname' => Aitsu_Translate :: translate('EditBox'),
			'enabled' => self :: getPosition($idart, 'editalt'),
			'position' => self :: getPosition($idart, 'editalt'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->pluginId = self :: ID;
		$idart = $this->getRequest()->getParam('idart');
		$idlang = Aitsu_Registry :: get()->session->currentLanguage;

		$data = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	artlang.idart, ' .
		'	catart.idcat, ' .
		'	artlang.idlang, ' .
		'	artlang.idartlang, ' .
		'	lang.idclient, 
							client.config ' .
		'from _art_lang as artlang ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'left join _lang as lang on artlang.idlang = lang.idlang 
						left join _clients as client on lang.idclient = client.idclient ' .
		'where ' .
		'	artlang.idart = :idart ' .
		'	and artlang.idlang = :idlang', array (
			':idart' => $idart,
			':idlang' => $idlang
		));

		Aitsu_Registry :: get()->env->idart = $data['idart'];
		Aitsu_Registry :: get()->env->idcat = $data['idcat'];
		Aitsu_Registry :: get()->env->idlang = $data['idlang'];
		Aitsu_Registry :: get()->env->lang = $data['idlang'];
		Aitsu_Registry :: get()->env->idartlang = $data['idartlang'];
		Aitsu_Registry :: get()->env->idclient = $data['idclient'];
		Aitsu_Registry :: get()->env->client = $data['idclient'];

		if (empty ($data['config'])) {
			$data['config'] = 'default';
		}
		Aitsu_Registry :: get()->config = Aitsu_Config_Ini :: getInstance('clients/' . $data['config']);

		Aitsu_Registry :: isEdit(true);
		Aitsu_Registry :: isBoxModel(true);
		$content = Aitsu_Ee_Transformation_Shortcode :: getInstance()->getContent('<script type="application/x-aitsu" src="Template:Root"></script>');

		$content = strip_tags($content, '<shortcode>,<code>');
		$content = preg_replace('/&[a-zA-Z]*;/', ' ', $content);
		$content = preg_replace('/\\s{2,}/s', ' ', $content);

		$doc = new DOMDocument();
		$doc->loadXML('<root><node>' . $content . '</node></root>');

		$xsl = new XSLTProcessor();
		$xsldoc = new DOMDocument();
		$xsldoc->load(dirname(__FILE__) . '/boxModelTransformer.xsl');
		$xsl->importStyleSheet($xsldoc);

		$this->view->content = $xsl->transformToXML($doc);
		$this->view->idart = $idart;
		$this->view->idartlang = $data['idartlang'];
	}

}
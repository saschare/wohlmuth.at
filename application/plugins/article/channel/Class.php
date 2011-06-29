<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class ChannelArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e0b1ba0-a604-4ae5-aca2-16927f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		$enabled = Aitsu_Db :: fetchOne('' .
		'select count(*) from _channel where idclient = :client', array (
			':client' => Aitsu_Registry :: get()->session->currentClient
		));

		return (object) array (
			'name' => 'channel',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Channel'),
			'enabled' => $enabled,
			'position' => self :: getPosition($idart, 'channel'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');
		$idartlang = Aitsu_Db :: fetchOne('' .
		'select idartlang from _art_lang ' .
		'where ' .
		'	idart = :idart ' .
		'	and idlang = :idlang', array (
			':idart' => $id,
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		$form = Aitsu_Forms :: factory('channels', APPLICATION_PATH . '/plugins/article/channel/forms/channel.ini');
		$form->title = Aitsu_Translate :: translate('Channels');
		$form->url = $this->view->url(array (
			'plugin' => 'channel',
			'paction' => 'index'
		), 'aplugin');

		$channels = array ();
		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	channelid, ' .
		'	name ' .
		'from _channel ' .
		'where ' .
		'	idclient = :client', array (
			':client' => Aitsu_Registry :: get()->session->currentClient
		));
		if ($results) {
			foreach ($results as $row) {
				$channels[] = (object) array (
					'value' => $row['channelid'],
					'name' => $row['name']
				);
			}
		}
		$form->setOptions('channels', $channels);

		$activeChannels = Aitsu_Db :: fetchCol('' .
		'select ' .
		'	channel.channelid ' .
		'from _channel_art_lang channel ' .
		'left join _art_lang artlang on channel.idartlang = artlang.idartlang ' .
		'where ' .
		'	idart = :idart ' .
		'	and idlang = :idlang', array (
			':idart' => $id,
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));
		$form->setValues(array (
			'channels' => $activeChannels,
			'idart' => $id
		));

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				Aitsu_Event :: raise('backend.article.edit.save.start', array (
					'idart' => $id
				));

				/*
				 * Persist the data.
				 */
				Aitsu_Db :: startTransaction();

				Aitsu_Db :: query('' .
				'delete from _channel_art_lang where idartlang = :idartlang', array (
					':idartlang' => $idartlang
				));

				$formData = $form->getValues();
				foreach ($formData['channels'] as $channel) {
					Aitsu_Db :: query('' .
					'insert into _channel_art_lang (idartlang, channelid) values (:idartlang, :channelid)', array (
						':idartlang' => $idartlang,
						'channelid' => $channel
					));
				}

				Aitsu_Db :: commit();

				$this->_helper->json((object) array (
					'success' => true
				));
			} else {
				$this->_helper->json((object) array (
					'success' => false,
					'errors' => $form->getErrors()
				));
			}
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'exception' => true,
				'message' => $e->getMessage()
			));
		}
	}

}
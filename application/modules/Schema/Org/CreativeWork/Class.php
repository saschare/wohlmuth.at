<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Schema_Org_CreativeWork_Class extends Aitsu_Module_SchemaOrg_Abstract {

	protected function _init() {
	}

	protected function _main() {

		$view = $this->_getView();

		return $view->render('index.phtml');
	}

	protected function _getView() {

		$view = parent :: _getView();

		$view->about = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'Thing', 'CreativeWork', 'about');
		$view->aggregateRating = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'AggregateRating', 'CreativeWork', 'aggregateRating');
		$view->audio = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'AudioObject', 'CreativeWork', 'audio');
		$view->author = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'PersonOrOrganization', 'CreativeWork', 'author');
		$view->awards = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.CreativeWork.Awards', 'Awards', 'CreativeWork');
		$view->contentLocation = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'Place', 'CreativeWork', 'contentLocation');
		$view->contentRating = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.CreativeWork.ContentRating', 'Content rating', 'CreativeWork');
		$view->datePublished = Aitsu_Content_Config_Date :: set($this->_index, 'schema.org.CreativeWork.DatePublished', 'Date published', 'CreativeWork');
		$view->editor = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'Person', 'CreativeWork', 'editor');
		$encodings = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.CreativeWork.Encodings', 'Encodings', 'CreativeWork');
		if (!empty ($encodings)) {
			$view->encodings = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'MediaObject', 'CreativeWork', $encodings);
		}
		$view->genre = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.CreativeWork.Genre', 'Genre', 'CreativeWork');
		$view->headline = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.CreativeWork.Headline', 'Headline', 'CreativeWork');
		$view->interactionCount = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.CreativeWork.InteractionCount', 'Interaction count', 'CreativeWork');
		$view->isFamilyFriendly = Aitsu_Content_Config_Radio :: set($this->_index, 'schema.org.CreativeWork.IsFamilyFriendly', 'Family friendly', array (
			'yes' => 'true',
			'no' => 'false'
		), 'CreativeWork');
		$view->keywords = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.CreativeWork.Keywords', 'Keywords', 'CreativeWork');
		$offers = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.CreativeWork.Offers', 'Offers', 'CreativeWork');
		if (!empty ($offers)) {
			$view->encodings = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'Offer', 'CreativeWork', $offers);
		}
		$view->publisher = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'Organization', 'CreativeWork', 'publisher');
		$reviews = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.CreativeWork.Reviews', 'Reviews', 'CreativeWork');
		if (!empty ($reviews)) {
			$view->encodings = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'Offer', 'CreativeWork', $reviews);
		}
		$view->video = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'VideoObject', 'CreativeWork', 'video');

		return $view;
	}
}
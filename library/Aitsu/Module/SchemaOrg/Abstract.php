<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Module_SchemaOrg_Abstract extends Aitsu_Module_Abstract {

	public static function init($context) {

		preg_match('/Module_Schema_Org_(.*?)_Class$/', $context['className'], $match);

		$schemaTree = array (
			'Thing' => array (
				'CreativeWork' => array (
					'Article' => array (
						'BlogPosting' => null,
						'NewsArticle' => null,
						'ScholarlyArticle' => null
					),
					'Blog' => null,
					'Book' => null,
					'ItemList' => null,
					'Map' => null,
					'MediaObject' => array (
						'AudioObject' => null,
						'ImageObject' => null,
						'MusicVideoObject' => null,
						'VideoObject' => null
					),
					'Movie' => null,
					'MusicPlaylist' => array (
						'MusicAlbum' => null
					),
					'MusicRecording' => null,
					'Painting' => null,
					'Photograph' => null,
					'Recipe' => null,
					'Review' => null,
					'Sculpture' => null,
					'TVEpisode' => null,
					'TVSeason' => null,
					'TVSeries' => null,
					'WebPage' => array (
						'AboutPage' => null,
						'CheckoutPage' => null,
						'CollectionPage' => array (
							'ImageGallery' => null,
							'VideoGallery' => null
						),
						'ContactPage' => null,
						'ItemPage' => null,
						'ProfilePage' => null,
						'SearchResultsPage' => null
					),
					'WebPageElement' => array (
						'SiteNavigationElement' => null,
						'Table' => null,
						'WPAdBlock' => null,
						'WPFooter' => null,
						'WPHeader' => null,
						'WPSideBar' => null
					)
				),
				'Event' => array (
					'BusinessEvent' => null,
					'ChildrensEvent' => null,
					'ComedyEvent' => null,
					'DanceEvent' => null,
					'EducationEvent' => null,
					'Festival' => null,
					'FoodEvent' => null,
					'LiteraryEvent' => null,
					'MusicEvent' => null,
					'SaleEvent' => null,
					'SocialEvent' => null,
					'SportsEvent' => null,
					'TheaterEvent' => null,
					'UserInteraction' => array (
						'UserBlocks' => null,
						'UserCheckins' => null,
						'UserComments' => null,
						'UserDownloads' => null,
						'UserLikes' => null,
						'UserPageVisits' => null,
						'UserPlays' => null,
						'UserPlusOnes' => null,
						'UserTweets' => null
					),
					'VisualArtsEvent' => null
				),
				'Intangible' => array (
					'Enumeration' => array (
						'BookFormatType' => null,
						'ItemAvailability' => null,
						'OfferItemCondition' => null
					),
					'Language' => null,
					'Offer' => array (
						'AggregateOffer' => null
					),
					'Quantity' => array (
						'Distance' => null,
						'Duration' => null,
						'Energy' => null,
						'Mass' => null
					),
					'Rating' => array (
						'AggregateRating' => null
					),
					'StructuredValue' => array (
						'ContactPoint' => array (
							' PostalAddress' => null
						),
						'GeoCoordinates' => null,
						'NutritionInformation' => null
					)
				),
				'Organization' => array (
					'Corporation' => null,
					'EducationalOrganization' => array (
						'CollegeOrUniversity' => null,
						'ElementarySchool' => null,
						'HighSchool' => null,
						'MiddleSchool' => null,
						'Preschool' => null,
						'School' => null
					),
					'GovernmentOrganization' => null,
					' LocalBusiness' => array ()
				),
				'Person' => array (),
				'Place' => array (),
				'Product' => array ()
			)
		);

		if (!empty ($context['params'])) {
			$params = Aitsu_Util :: parseSimpleIni($context['params']);
		}

		if (isset ($params->genuineType)) {
			$genuineType = $params->genuineType;
		} else {
			$genuineType = $match[1];
		}

		$types = array ();
		self :: _getChildrenOf($genuineType, $types, $schemaTree);
		ksort($types);

		$index = preg_replace('/[^a-zA-Z_0-9]/', '_', $context['index']);
		$index = str_replace('.', '_', $index);

		$type = Aitsu_Content_Config_Select :: set($index, 'schema.org.Type', 'Subtype', $types, 'Type');

		if (!empty ($type) && $type != $match[1]) {
			/*
			 * A subtype has to be used instead of the genuine one.
			 */
			return '' .
			'<script type="application/x-aitsu" src="Schema.Org.' . $type . ':' . $context['index'] . '">' . "\n" .
			'	genuineType = ' . $genuineType . '' . "\n" . $context['params'] . "\n" .
			'</script>';
		}

		$output = parent :: init($context);

		if (Aitsu_Application_Status :: isEdit()) {
			$maxLength = 60;
			$index = strlen($context['index']) > $maxLength ? substr($context['index'], 0, $maxLength) . '...' : $context['index'];

			return '' .
			'<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
			'<div style="border:1px dashed #CCC; padding:2px 2px 0 2px;">' .
			'	<div style="height:15px; background-color: #CCC; color: white; font-size: 11px; padding:2px 5px 0 5px;">' .
			'		<span style="font-weight:bold; float:left;">' . $index . '</span><span style="float:right;">schema.org <span style="font-weight:bold;">' . $match[1] . '</span></span>' .
			'	</div>' .
			'	<div style="padding:5px 3px 5px 3px;">' .
			'		' . $output . '' .
			'	</div>' .
			'</div>';
		}

		return $output;
	}

	protected function _getView() {

		$view = parent :: _getView(new Aitsu_Module_SchemaOrg_View());

		preg_match('/Module_Schema_Org_(.*?)_Class/', get_class($this), $match);
		$view->SchemaOrgType = $match[1];

		$view->idart = Aitsu_Registry :: get()->env->idart;
		$view->description = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.Thing.Description', 'Description', 'Thing');

		$images = Aitsu_Db :: fetchCol('' .
		'select distinct image.filename ' .
		'from _media image ' .
		'where ' .
		'	image.deleted is null ' .
		'	and image.idart = :idart ' .
		'order by ' .
		'	image.filename', array (
			':idart' => Aitsu_Registry :: get()->env->idart
		));

		if (!$images) {
			$view->image = null;
		} else {
			$keyValuePairs = array (
				'No image' => ''
			);
			foreach ($images as $image) {
				$keyValuePairs[$image] = $image;
			}
			$view->image = Aitsu_Content_Config_Select :: set($this->_index, 'schema.org.Thing.Image', 'Image', $keyValuePairs, 'Thing');
		}

		if (!in_array($view->image, $images)) {
			$view->image = null;
		}

		$view->name = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Thing.Name', 'Name', 'Thing');
		$view->url = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Thing.URL', 'URL', 'Thing');

		return $view;
	}

	protected static function _getChildrenOf($type, & $result, & $subSet, $in = false) {

		foreach ($subSet as $key => $value) {
			if ($in || $type == $key) {
				$result[$key] = $key;
			}
			if (is_array($value)) {
				self :: _getChildrenOf($type, $result, $value, $in || $type == $key);
			}
		}
	}
}
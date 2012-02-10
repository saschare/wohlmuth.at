<?php


/**
 * @author Christian Kehres, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 */
class Adm_Script_PubArtMetaSchemaOrgTypeReplace0WithNull extends Aitsu_Adm_Script_Abstract {

	public static function getName() {

		return Aitsu_Translate :: translate('pub_art_meta.schemaorgtype, replace 0 with null');
	}

	public function doRemoveBug() {

		$count = Aitsu_Db :: query('' .
		'update _pub_art_meta ' .
		'set schemaorgtype = replace(schemaorgtype, 0, NULL) ' .
		'where schemaorgtype = 0')->rowCount();

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('All occurences of 0 have been replaced by null in pub_art_meta.schemaorgtype. %s rows have been updated.'), $count));
	}

}
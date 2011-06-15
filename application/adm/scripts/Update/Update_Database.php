<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */
class Adm_Script_Update_Database extends Aitsu_Adm_Script_Abstract {

	public static function getName() {

		return Aitsu_Translate :: translate('Actualize database structure');
	}

	public function doAddTablePub() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'pub';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _pub (' .
		'`pubid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,' .
		'`idartlang` INT(10) UNSIGNED NOT NULL,' .
		'`userid` INT(10) UNSIGNED NOT NULL,' .
		'`pubtime` DATETIME NOT NULL,' .
		'`status` TINYINT(4) NOT NULL DEFAULT \'1\',' .
		'PRIMARY KEY (`pubid`),' .
		'INDEX `idartlang` (`idartlang` ASC),' .
		'CONSTRAINT _pub_ibfk_1 ' .
		'FOREIGN KEY (`idartlang` ) REFERENCES _art_lang (`idartlang` ) ON DELETE CASCADE) ' .
		'ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARACTER SET = utf8');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTablePubAitsuArticleProperty() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'pub_aitsu_article_property';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _pub_aitsu_article_property (' .
		'`propertyid` INT(10) UNSIGNED NOT NULL ,  ' .
		'`idartlang` INT(10) UNSIGNED NOT NULL ,  ' .
		'`textvalue` TEXT NULL DEFAULT NULL ,  ' .
		'`floatvalue` DOUBLE NULL DEFAULT NULL ,  ' .
		'`datevalue` DATETIME NULL DEFAULT NULL ,  ' .
		'`pubid` INT(10) UNSIGNED NOT NULL ,  ' .
		'`status` TINYINT(4) NOT NULL ,  ' .
		'PRIMARY KEY (`propertyid`, `idartlang`, `pubid`) ,  ' .
		'INDEX `idartlang` (`idartlang` ASC) ,  ' .
		'INDEX `pubid` (`pubid` ASC) ,  ' .
		'CONSTRAINT _pub_aitsu_article_property_ibfk_1 ' .
		'FOREIGN KEY (`propertyid` ) REFERENCES _aitsu_property (`propertyid` ) ON DELETE CASCADE, ' .
		'CONSTRAINT _pub_aitsu_article_property_ibfk_2 ' .
		'FOREIGN KEY (`idartlang` ) REFERENCES _art_lang (`idartlang` ) ON DELETE CASCADE, ' .
		'CONSTRAINT _pub_aitsu_article_property_ibfk_3 ' .
		'FOREIGN KEY (`pubid` ) REFERENCES _pub (`pubid` ) ON DELETE CASCADE) ' .
		'ENGINE = InnoDB DEFAULT CHARACTER SET = utf8');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTablePubArtLang() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'pub_art_lang';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _pub_art_lang (' .
		'`idartlang` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,  ' .
		'`idart` INT(10) UNSIGNED NOT NULL DEFAULT \'0\' ,  ' .
		'`idlang` INT(10) UNSIGNED NOT NULL DEFAULT \'0\' ,  ' .
		'`title` VARCHAR(255) NOT NULL ,  ' .
		'`urlname` VARCHAR(255) NOT NULL ,  ' .
		'`pagetitle` VARCHAR(255) NOT NULL ,  ' .
		'`teasertitle` VARCHAR(255) NOT NULL ,  ' .
		'`summary` TEXT NULL DEFAULT NULL ,  ' .
		'`created` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\' ,  ' .
		'`lastmodified` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\' ,  ' .
		'`online` TINYINT(1) NOT NULL DEFAULT \'0\' ,  ' .
		'`pubfrom` TIMESTAMP NULL DEFAULT NULL ,  ' .
		'`pubuntil` TIMESTAMP NULL DEFAULT NULL ,  ' .
		'`published` TINYINT(3) UNSIGNED NULL DEFAULT NULL ,  ' .
		'`redirect` TINYINT(4) NOT NULL DEFAULT \'0\' ,  ' .
		'`redirect_url` VARCHAR(255) NULL DEFAULT NULL ,  ' .
		'`artsort` INT(10) NOT NULL DEFAULT \'0\' ,  ' .
		'`locked` INT(1) NOT NULL DEFAULT \'0\' ,  ' .
		'`configsetid` INT(10) UNSIGNED NULL DEFAULT NULL ,  ' .
		'`config` TEXT NULL DEFAULT NULL ,  ' .
		'`pubid` INT(10) UNSIGNED NOT NULL ,  ' .
		'`status` TINYINT(4) NOT NULL ,  ' .
		'`mainimage` VARCHAR(255) NULL DEFAULT NULL,  ' .
		'PRIMARY KEY (`idartlang`, `pubid`) ,  ' .
		'INDEX `idlang` (`idlang` ASC) ,  ' .
		'INDEX `title` (`title` ASC) ,  ' .
		'INDEX `pagetitle` (`pagetitle` ASC) ,  ' .
		'INDEX `configsetid` (`configsetid` ASC) ,  ' .
		'INDEX `pubid` (`pubid` ASC) ,  ' .
		'INDEX `idart` (`idart` ASC) ,  ' .
		'CONSTRAINT _pub_art_lang_ibfk_1 FOREIGN KEY (`idartlang` ) REFERENCES _art_lang (`idartlang` ) ON DELETE CASCADE,  ' .
		'CONSTRAINT _pub_art_lang_ibfk_4 FOREIGN KEY (`configsetid` ) REFERENCES _configset (`configsetid` )  ON DELETE SET NULL, ' .
		'CONSTRAINT _pub_art_lang_ibfk_5 FOREIGN KEY (`pubid` ) REFERENCES _pub (`pubid` ) ON DELETE CASCADE,  ' .
		'CONSTRAINT _pub_art_lang_ibfk_6 FOREIGN KEY (`idart` ) REFERENCES _art (`idart` ) ON DELETE CASCADE, ' .
		'CONSTRAINT _pub_art_lang_ibfk_7 FOREIGN KEY (`idlang` ) REFERENCES _lang (`idlang` ) ON DELETE CASCADE)' .
		'ENGINE = InnoDB AUTO_INCREMENT = 506 DEFAULT CHARACTER SET = utf8');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTablePubArtMeta() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'pub_art_meta';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _pub_art_meta (' .
		'`idartlang` INT(10) UNSIGNED NOT NULL ,  ' .
		'`description` TEXT NOT NULL ,  ' .
		'`author` VARCHAR(255) NOT NULL ,  ' .
		'`keywords` TEXT NOT NULL ,  ' .
		'`date` DATE NULL DEFAULT NULL ,  ' .
		'`expires` DATE NULL DEFAULT NULL ,  ' .
		'`robots` VARCHAR(255) NOT NULL ,  ' .
		'`dctitle` VARCHAR(255) NOT NULL ,  ' .
		'`dccreator` VARCHAR(255) NOT NULL ,  ' .
		'`dcsubject` VARCHAR(255) NOT NULL ,  ' .
		'`dcpublisher` VARCHAR(255) NOT NULL ,  ' .
		'`dccontributor` VARCHAR(255) NOT NULL ,  ' .
		'`dcdate` VARCHAR(255) NOT NULL ,  ' .
		'`dctype` VARCHAR(255) NOT NULL ,  ' .
		'`dcformat` VARCHAR(255) NOT NULL ,  ' .
		'`dcidentifier` VARCHAR(255) NOT NULL ,  ' .
		'`dcsource` VARCHAR(255) NOT NULL ,  ' .
		'`dclanguage` VARCHAR(255) NOT NULL ,  ' .
		'`dcrelation` VARCHAR(255) NOT NULL ,  ' .
		'`cdcoverage` VARCHAR(255) NOT NULL ,  ' .
		'`dcrights` VARCHAR(255) NOT NULL ,  ' .
		'`pubid` INT(10) UNSIGNED NOT NULL ,  ' .
		'`status` TINYINT(4) NOT NULL ,  ' .
		'PRIMARY KEY (`idartlang`, `pubid`) ,  ' .
		'INDEX `pubid` (`pubid` ASC) ,  ' .
		'CONSTRAINT _pub_art_meta_ibfk_1 FOREIGN KEY (`idartlang` ) REFERENCES _art_lang (`idartlang` ) ON DELETE CASCADE,  ' .
		'CONSTRAINT _pub_art_meta_ibfk_2 FOREIGN KEY (`pubid` ) REFERENCES _pub (`pubid` ) ON DELETE CASCADE)' .
		'ENGINE = InnoDB DEFAULT CHARACTER SET = utf8');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTablePubArticleContent() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'pub_article_content';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _pub_article_content (' .
		'`idartlang` INT(10) UNSIGNED NOT NULL ,  ' .
		'`index` VARCHAR(20) NOT NULL ,  ' .
		'`value` LONGTEXT NOT NULL ,  ' .
		'`modified` DATETIME NOT NULL ,  ' .
		'`pubid` INT(10) UNSIGNED NOT NULL ,  ' .
		'`status` TINYINT(4) NOT NULL DEFAULT \'1\' ,  ' .
		'PRIMARY KEY (`idartlang`, `index`, `pubid`) ,  ' .
		'INDEX `pubid` (`pubid` ASC) ,  ' .
		'CONSTRAINT _pub_article_content_ibfk_1 FOREIGN KEY (`idartlang` ) REFERENCES _art_lang (`idartlang` ) ON DELETE CASCADE,  ' .
		'CONSTRAINT _pub_article_content_ibfk_2 FOREIGN KEY (`pubid` ) REFERENCES _pub (`pubid` ) ON DELETE CASCADE)' .
		'ENGINE = InnoDB DEFAULT CHARACTER SET = utf8');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doRestorePublicationViews() {

		try {
			Aitsu_Db :: query('' .
			'drop view if exists _pubv_article_content');

			Aitsu_Db :: query('' .
			'CREATE VIEW _pubv_article_content AS ' .
			'select ' .
			'	`idartlang` AS `idartlang`,' .
			'	`index` AS `index`,' .
			'	`value` AS `value`,' .
			'	`modified` AS `modified`,' .
			'	`pubid` AS `pubid`,' .
			'	`status` AS `status` ' .
			'from _pub_article_content ' .
			'where (`status` = 1);');

			Aitsu_Db :: query('' .
			'drop view if exists _pubv_art_lang');

			Aitsu_Db :: query('' .
			'CREATE VIEW _pubv_art_lang AS ' .
			'select `idartlang` AS `idartlang`,' .
			'`idart` AS `idart`,' .
			'`idlang` AS `idlang`,' .
			'`title` AS `title`,' .
			'`urlname` AS `urlname`,' .
			'`pagetitle` AS `pagetitle`,' .
			'`teasertitle` AS `teasertitle`,' .
			'`summary` AS `summary`,' .
			'`created` AS `created`,' .
			'`lastmodified` AS `lastmodified`,' .
			'`online` AS `online`,' .
			'`pubfrom` AS `pubfrom`,' .
			'`pubuntil` AS `pubuntil`,' .
			'`published` AS `published`,' .
			'`redirect` AS `redirect`,' .
			'`redirect_url` AS `redirect_url`,' .
			'`artsort` AS `artsort`,' .
			'`locked` AS `locked`,' .
			'`configsetid` AS `configsetid`,' .
			'`config` AS `config`,' .
			'`pubid` AS `pubid`,' .
			'`status` AS `status`, ' .
			'`mainimage` AS `mainimage` ' .
			'from _pub_art_lang ' .
			'where (`status` = 1);');

			Aitsu_Db :: query('' .
			'drop view if exists _pubv_art_meta');

			Aitsu_Db :: query('' .
			'CREATE VIEW _pubv_art_meta AS ' .
			'select `idartlang` AS `idartlang`,' .
			'`description` AS `description`,' .
			'`author` AS `author`,' .
			'`keywords` AS `keywords`,' .
			'`date` AS `date`,' .
			'`expires` AS `expires`,' .
			'`robots` AS `robots`,' .
			'`dctitle` AS `dctitle`,' .
			'`dccreator` AS `dccreator`,' .
			'`dcsubject` AS `dcsubject`,' .
			'`dcpublisher` AS `dcpublisher`,' .
			'`dccontributor` AS `dccontributor`,' .
			'`dcdate` AS `dcdate`,' .
			'`dctype` AS `dctype`,' .
			'`dcformat` AS `dcformat`,' .
			'`dcidentifier` AS `dcidentifier`,' .
			'`dcsource` AS `dcsource`,' .
			'`dclanguage` AS `dclanguage`,' .
			'`dcrelation` AS `dcrelation`,' .
			'`cdcoverage` AS `cdcoverage`,' .
			'`dcrights` AS `dcrights`,' .
			'`pubid` AS `pubid`,' .
			'`status` AS `status` ' .
			'from _pub_art_meta ' .
			'where (`status` = 1);');

			Aitsu_Db :: query('' .
			'drop view if exists _pubv_aitsu_article_property');

			Aitsu_Db :: query('' .
			'CREATE VIEW _pubv_aitsu_article_property AS ' .
			'select `propertyid` AS `propertyid`,' .
			'`idartlang` AS `idartlang`,' .
			'`textvalue` AS `textvalue`,' .
			'`floatvalue` AS `floatvalue`,' .
			'`datevalue` AS `datevalue`,' .
			'`pubid` AS `pubid`,' .
			'`status` AS `status` ' .
			'from _pub_aitsu_article_property ' .
			'where (`status` = 1);');

			return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('Views restored.'));
		} catch (Exception $e) {
			return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('Views have not been restored. Please create view with a tool of your choice.'));
		}
	}

	public function doAddTableRatings() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $pf . 'ratings'
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $pf . 'ratings'));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _ratings (' .
		'`idartlang` int(10) unsigned NOT NULL,' .
		'`rated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,' .
		'`ip` varchar(20) NOT NULL,' .
		'`rate` float NOT NULL,' .
		'PRIMARY KEY (`idartlang`,`ip`)' .
		') ENGINE=InnoDB DEFAULT CHARSET=utf8');

		Aitsu_Db :: query('' .
		'ALTER TABLE _ratings ' .
		'ADD CONSTRAINT _ratings_ibfk_1 ' .
		'FOREIGN KEY (`idartlang`) ' .
		'REFERENCES _art_lang (`idartlang`) ' .
		'ON DELETE CASCADE;');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $pf . 'ratings'));
	}

	public function doAddTableRating() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $pf . 'rating'
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $pf . 'rating'));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _rating (' .
		'`idartlang` int(10) unsigned NOT NULL,' .
		'`rating` float NOT NULL,' .
		'`votes` int(11) NOT NULL DEFAULT \'1\',' .
		'PRIMARY KEY (`idartlang`),' .
		'KEY `rating` (`rating`)' .
		') ENGINE=InnoDB DEFAULT CHARSET=utf8');

		Aitsu_Db :: query('' .
		'ALTER TABLE _rating ' .
		'ADD CONSTRAINT _rating_ibfk_1 ' .
		'FOREIGN KEY (`idartlang`) ' .
		'REFERENCES _art_lang (`idartlang`) ' .
		'ON DELETE CASCADE');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $pf . 'rating'));
	}

	public function doAddTodo() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'todo';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _todo (' .
		'`todoid` int(10) unsigned NOT NULL AUTO_INCREMENT,' .
		'`idartlang` int(10) unsigned NOT NULL,' .
		'`status` tinyint(4) NOT NULL DEFAULT \'0\',' .
		'`title` varchar(255) NOT NULL,' .
		'`description` text NOT NULL,' .
		'`duedate` datetime DEFAULT NULL,' .
		'`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,' .
		'PRIMARY KEY (`todoid`),' .
		'KEY `idartlang` (`idartlang`)' .
		') ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTableHoneytrap() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'honeytrap';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _honeytrap (' .
		'`trapid` int(10) unsigned NOT NULL AUTO_INCREMENT,' .
		'`ip` varchar(15) NOT NULL,' .
		'`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,' .
		'PRIMARY KEY (`trapid`),' .
		'KEY `ip` (`ip`),' .
		'KEY `created` (`created`)' .
		') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAlterTableMedia() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'media';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.columns ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName ' .
		'	and column_name = :columnName ', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table,
			':columnName' => 'xtl'
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s is already up to date.'), $table));
		}

		Aitsu_Db :: query('' .
		'ALTER TABLE  _media ADD  `xtl` FLOAT NOT NULL DEFAULT \'0\' AFTER  `extension`,' .
		'ADD  `ytl` FLOAT NOT NULL DEFAULT  \'0\' AFTER  `xtl` ,' .
		'ADD  `xbr` FLOAT NOT NULL DEFAULT  \'1\' AFTER  `ytl` ,' .
		'ADD  `ybr` FLOAT NOT NULL DEFAULT  \'1\' AFTER  `xbr`');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s altered.'), $table));
	}

	public function doAlterTableMediaTags() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'media_tags';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.columns ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName ' .
		'	and column_name = :columnName ', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table,
			':columnName' => 'val'
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s is already up to date.'), $table));
		}

		Aitsu_Db :: query('' .
		'ALTER TABLE _media_tags ADD  `val` VARCHAR( 255 ) NULL DEFAULT NULL');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s altered.'), $table));
	}

	public function doAlterTableArtLang() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'art_lang';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.columns ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName ' .
		'	and column_name = :columnName ', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table,
			':columnName' => 'mainimage'
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s is already up to date.'), $table));
		}

		Aitsu_Db :: query('' .
		'ALTER TABLE _art_lang ADD  `mainimage` VARCHAR( 255 ) NULL DEFAULT NULL');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s altered.'), $table));
	}

	public function doAlterTableArtLangPub() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'pub_art_lang';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.columns ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName ' .
		'	and column_name = :columnName ', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table,
			':columnName' => 'mainimage'
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s is already up to date.'), $table));
		}

		Aitsu_Db :: query('' .
		'ALTER TABLE _pub_art_lang ADD  `mainimage` VARCHAR( 255 ) NULL DEFAULT NULL');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s altered.'), $table));
	}

	public function doAddSyndicationSource() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'syndication_source';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _syndication_source (' .
		'`sourceid` int(10) unsigned NOT NULL AUTO_INCREMENT,' .
		'`idclient` int(10) unsigned NOT NULL,' .
		'`url` varchar(255) NOT NULL,' .
		'`userid` varchar(255) NOT NULL,' .
		'`secret` varchar(255) NOT NULL,' .
		'PRIMARY KEY (`sourceid`),' .
		'KEY `idclient` (`idclient`)' .
		') ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

		Aitsu_Db :: query('' .
		'ALTER TABLE _syndication_source ' .
		'ADD CONSTRAINT `ait_syndication_source_ibfk_1` FOREIGN KEY (`idclient`) REFERENCES _clients (`idclient`) ON DELETE CASCADE');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTableSyndicationSource() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'syndication_source';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _syndication_source (' .
		'`sourceid` int(10) unsigned NOT NULL AUTO_INCREMENT,' .
		'`idclient` int(10) unsigned NOT NULL,' .
		'`url` varchar(255) NOT NULL,' .
		'`userid` varchar(255) NOT NULL,' .
		'`secret` varchar(255) NOT NULL,' .
		'PRIMARY KEY (`sourceid`),' .
		'KEY `idclient` (`idclient`)' .
		') ENGINE=InnoDB  DEFAULT CHARSET=utf8');

		Aitsu_Db :: query('' .
		'ALTER TABLE _syndication_source ' .
		'ADD CONSTRAINT `ait_syndication_source_ibfk_1` ' .
		'FOREIGN KEY (`idclient`) ' .
		'REFERENCES _clients (`idclient`) ' .
		'ON DELETE CASCADE');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTableSyndicationResource() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'syndication_resource';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _syndication_resource (' .
		'`sourceid` int(10) unsigned NOT NULL,' .
		'`sourceidartlang` int(10) unsigned NOT NULL,' .
		'`name` varchar(255) DEFAULT NULL,' .
		'`content` longtext NOT NULL,' .
		'`loaded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,' .
		'PRIMARY KEY (`sourceid`,`sourceidartlang`)' .
		') ENGINE=InnoDB DEFAULT CHARSET=utf8');

		Aitsu_Db :: query('' .
		'ALTER TABLE _syndication_resource ' .
		'ADD CONSTRAINT `ait_syndication_resource_ibfk_1` ' .
		'FOREIGN KEY (`sourceid`) REFERENCES _syndication_source (`sourceid`) ' .
		'ON DELETE CASCADE');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAddTableSyndicationResourceArt() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'syndication_resource_art';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.tables ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s already exists.'), $table));
		}

		Aitsu_Db :: query('' .
		'CREATE TABLE IF NOT EXISTS _syndication_resource_art (' .
		'`idartlang` int(10) unsigned NOT NULL,' .
		'`sourceid` int(10) unsigned NOT NULL,' .
		'`sourceidartlang` int(10) unsigned NOT NULL,' .
		'PRIMARY KEY (`idartlang`,`sourceid`,`sourceidartlang`),' .
		'KEY `sourceid` (`sourceid`)' .
		') ENGINE=InnoDB DEFAULT CHARSET=utf8');

		Aitsu_Db :: query('' .
		'ALTER TABLE _syndication_resource_art ' .
		'ADD CONSTRAINT `ait_syndication_resource_art_ibfk_1` ' .
		'FOREIGN KEY (`idartlang`) ' .
		'REFERENCES _art_lang (`idartlang`) ON DELETE CASCADE, ' .
		'ADD CONSTRAINT `ait_syndication_resource_art_ibfk_2` ' .
		'FOREIGN KEY (`sourceid`) ' .
		'REFERENCES _syndication_source (`sourceid`) ON DELETE CASCADE');

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s added.'), $table));
	}

	public function doAlterTableLang() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'lang';

		$exists = Aitsu_Db :: fetchOne('' .
		'select count(*) from information_schema.columns ' .
		'where ' .
		'	table_schema = :schema ' .
		'	and table_name = :tableName ' .
		'	and column_name = :columnName ', array (
			':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
			':tableName' => $table,
			':columnName' => 'longname'
		));

		if ($exists == 1) {
			return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s is already up to date.'), $table));
		}

		Aitsu_Db :: query("ALTER TABLE `_lang` ADD `longname` VARCHAR(255) NOT NULL AFTER `name`");

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s altered.'), $table));
	}

	public function doAlterTableGlobalMedia() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'media';

		try {
			Aitsu_Db :: query("ALTER TABLE `_media` DROP FOREIGN KEY `ait_media_ibfk_1`");
			Aitsu_Db :: query("ALTER TABLE `_media` CHANGE `idart` `idart` INT( 10 ) UNSIGNED NULL;");
			Aitsu_Db :: query("ALTER TABLE `_media` ADD FOREIGN KEY ( `idart` ) REFERENCES `aitsu_aitsu`.`ait_art` (`idart`) ON DELETE CASCADE;");
		} catch (Exception $e) {
			// Do nothing. An exception occurs too, if the foreign key does not exist.
		}

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s altered.'), $table));
	}
        
        public function doAlterTableToDo() {

		$pf = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$table = $pf . 'todo';
                
		try {
			Aitsu_Db :: query("ALTER TABLE `_todo` ADD `userid` INT(10) UNSIGNED NOT NULL AFTER `idartlang`");
		} catch (Exception $e) {
			// Do nothing. An exception occurs too, if the foreign key does not exist.
		}

		return Aitsu_Adm_Script_Response :: factory(sprintf(Aitsu_Translate :: translate('Table %s altered.'), $table));
	}

}
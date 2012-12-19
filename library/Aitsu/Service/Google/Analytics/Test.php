<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 * 
 * php application/cli/index.php -s Test -t Aitsu_Service_Google_Analytics_Test
 * 
 * Pendings:
 * - Fehlercode automatisch setzen (Bootstrap)
 * - Transaction und Items setzen (Shop, Module_Shop_Payment_Class, _initSucccess)
 * - Ausgabe vornehmen (Modul)
 */
class Aitsu_Service_Google_Analytics_Test {

	public static function run() {

		$transaction = Aitsu_Service_Google_Analytics_Transaction :: add('123', 'domain.tld', '100', '8', '0', 'Bern', 'Bern', 'Schweiz');
		$transaction->addItem('123.456', 'Produkt1', 'Kategorie1', '10', '1');
		$transaction->addItem('123.789', 'Produkt2', 'Kategorie2', '10', '1');
		$transaction->addItem('123.012', 'Produkt3', 'Kategorie3', '10', '1');
		$transaction = Aitsu_Service_Google_Analytics_Transaction :: add('456', 'domain.tld', '100', '8', '0', 'Bern', 'Bern', 'Schweiz');
		$transaction->addItem('123.456', 'Produkt1', 'Kategorie1', 'fdfsdfs10', '1');
		$transaction->addItem('123.789', 'Pro"duk\'t2', 'Kategorie2', '10', '1');
		$transaction->addItem('123.012', 'Produkt3', 'Kategorie3', '10', '1');
		$transaction = Aitsu_Service_Google_Analytics_Transaction :: add(null, null, '100', '8', '0', 'Bern', 'Bern', 'Schweiz');
		$transaction->addItem('123.456', 'Produ' . "\n" . 'kt1', 'Kategorie1', '10', '1');
		$transaction->addItem('123.789', 'Produkt2', 'Kategorie2', '10', '1');
		$transaction->addItem('123.012', 'Produkt3', 'Kategorie3', '10', '1');

		// Aitsu_Service_Google_Analytics_Event :: add('Error', '404', new Aitsu_Service_Google_Analytics_Javascript('page: \' + document.location.pathname + document.location.search + \' ref: \' + document.referrer'), 1, true);
		// Aitsu_Service_Google_Analytics_Event :: add('Error', '403', new Aitsu_Service_Google_Analytics_Javascript('page: \' + document.location.pathname + document.location.search + \' ref: \' + document.referrer'), 1, true);
		// Aitsu_Service_Google_Analytics_Error :: add('500');

		echo Aitsu_Service_Google_Analytics :: getScript();
	}
}
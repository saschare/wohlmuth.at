<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 * 
 * php application/cli/index.php -s Test -t Aitsu_Service_Google_Analytics_Test
 */
class Aitsu_Service_Google_Analytics_Test {
	
	public static function run() {
		
		$transaction = Aitsu_Service_Google_Analytics_Transaction :: add('123', 'domain.tld', '100', '8', '0', 'Bern', 'Bern', 'Schweiz');
		$transaction->addItem('123.456', 'Produkt1', 'Kategorie1', '10', '1');
		$transaction->addItem('123.789', 'Produkt2', 'Kategorie2', '10', '1');
		$transaction->addItem('123.012', 'Produkt3', 'Kategorie3', '10', '1');
		$transaction = Aitsu_Service_Google_Analytics_Transaction :: add('456', 'domain.tld', '100', '8', '0', 'Bern', 'Bern', 'Schweiz');
		$transaction->addItem('123.456', 'Produkt1', 'Kategorie1', '10', '1');
		$transaction->addItem('123.789', 'Produkt2', 'Kategorie2', '10', '1');
		$transaction->addItem('123.012', 'Produkt3', 'Kategorie3', '10', '1');
		$transaction = Aitsu_Service_Google_Analytics_Transaction :: add(null, null, '100', '8', '0', 'Bern', 'Bern', 'Schweiz');
		$transaction->addItem('123.456', 'Produkt1', 'Kategorie1', '10', '1');
		$transaction->addItem('123.789', 'Produkt2', 'Kategorie2', '10', '1');
		$transaction->addItem('123.012', 'Produkt3', 'Kategorie3', '10', '1');
		
		echo Aitsu_Service_Google_Analytics_Transaction :: getPush();
	}
}
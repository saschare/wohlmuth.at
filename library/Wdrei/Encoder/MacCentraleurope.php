<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Wdrei_Encoder_MacCentraleurope implements Wdrei_Encoder_Interface {

	public function encode($text) {

		return iconv('mac-centraleurope', 'UTF-8', $text);
	}
}
<?php


/**
 * Google Maps Javascript API implementation.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Custom_Interbrain_Perfect2_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$view = $this->_getView();

		$client = new SoapClient('http://212.25.31.113/p2online/P2online.asmx?wsdl', array (
			'classmap' => array (
				'OfferItem' => 'Wdrei_Interbrain_Perfect2_OfferItem',
				'OfferPosition' => 'Wdrei_Interbrain_Perfect2_OfferItem',
				'ClientData' => 'Wdrei_Interbrain_Perfect2_ClientData',
				'OrderPosition' => 'Wdrei_Interbrain_Perfect2_OrderPosition',
				'Order' => 'Wdrei_Interbrain_Perfect2_Order'
			),
			'trace' => true
		));

		$voucher = $client->GetVoucher(0, 'SD');

		// $view->data = $client->__getLastRequest();
		// $view->data = $voucher->GetVoucherResult->Offer->Items->OfferPosition[0]->ValidFrom;
		// return $view->render('index.phtml');

		$itemId = $voucher->GetVoucherResult->Offer->Items->OfferPosition[0]->ItemId;

		$view->data = $client->ValidateOrder(0, Wdrei_Interbrain_Perfect2_Order :: instance(array (
			'Items' => array (
				Wdrei_Interbrain_Perfect2_OrderPosition :: instance(array (
					'Id' => 1,
					'Client' => Wdrei_Interbrain_Perfect2_ClientData :: instance(array (
						'EMailAddress' => 'a.kummer@wdrei.ch'
					)),
					'ItemID' => $voucher->GetVoucherResult->Offer->Items->OfferPosition[0]->ItemId,
					'ItemLanguage' => 'SD',
					'Quantity' => 1,
					'ValidFrom' => $voucher->GetVoucherResult->Offer->Items->OfferPosition[0]->ValidFrom->From,
					'ValidTo' => $voucher->GetVoucherResult->Offer->Items->OfferPosition[0]->ValidFrom->To
				))
			)
		)));

		$view->data = $client->__getLastRequest();
		return $view->render('index.phtml');
	}

}
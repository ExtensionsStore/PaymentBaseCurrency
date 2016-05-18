<?php
/**
 * Append default base currency code to process amount
 *
 * @category   ExtensionsStore
 * @package    ExtensionsStore_PaymentBaseCurrency
 * @author     Extensions Store <admin@extensions-store.com>
 */
class ExtensionsStore_PaymentBaseCurrency_Block_Paygate_Authorizenet_Info_Cc extends Mage_Paygate_Block_Authorizenet_Info_Cc {
	/**
	 *
	 * @return array
	 */
	public function getCards() {
		$cards = parent::getCards ();
		foreach ( $cards as $i => $card ) {
			$processedAmount = $card ['Processed Amount'];
			preg_match ( '/([\d,.]+)/', $processedAmount, $matches );
			if (isset ( $matches [1] )) {
				$rawProcessedAmount = $matches [1];
				$defaultCurrencyCode = Mage::getStoreConfig('currency/options/base',0);
				$cards [$i] ['Processed Amount'] = '$' . $rawProcessedAmount . ' '.$defaultCurrencyCode;
			}
		}
		return $cards;
	}
}

<?php
/**
 *
 *
 * @category   ExtensionsStore
 * @package    ExtensionsStore_PaymentBaseCurrency
 * @author     Extensions Store <admin@extensions-store.com>
 */
class ExtensionsStore_PaymentBaseCurrency_Model_Observer {
	/**
	 * Change base currency to default base currency for authorizenet and amazon_payments
	 *
	 * @see sales_order_payment_capture
	 * @param Mage_Sales_Model_Order_Payment $payment        	
	 */
	public function changeOrderCurrency($observer) {
		$order = $observer->getOrder ();
		$store = $order->getStore ();
		$storeId = $store->getId();
		
		$defaultCurrencyCode = Mage::getStoreConfig('currency/options/base',0);
		$authorizenetConvert = Mage::getStoreConfig('payment/authorizenet/convert_usd', $storeId);
		$authorizenetDirectConvert = Mage::getStoreConfig('payment/authorizenet_directpost/convert_usd', $storeId);
		$amazonConvert = Mage::getStoreConfig('payment/ap_credentials/convert_usd', $storeId);
		
		if ($authorizenetConvert || $authorizenetDirectConvert || $amazonConvert){
			
			$payment = $order->getPayment ();
			$method = $payment->getMethod ();
			
			if (($authorizenetConvert && $method == 'authorizenet') || 
					($authorizenetDirectConvert && $method == 'authorizenet_directpost') || 
					($amazonConvert && $method == 'amazon_payments')) {
			
				$orderBaseCurrencyCode = $order->getBaseCurrencyCode ();
					
				if ($orderBaseCurrencyCode != $defaultCurrencyCode) {
			
					$helper = Mage::helper ( 'paymentbasecurrency' );
					$store = Mage::app ()->getStore ();
					$originalBaseCurrencyCode = $store->getConfig ( 'currency/options/base' );
					$store->setConfig ( 'currency/options/base', $defaultCurrencyCode);
			
					$order->setBaseCurrencyCode ( $defaultCurrencyCode );
					$order->setBaseToGlobalRate ( 1 );
			
					// convert payment base amounts
					$helper->convertBaseValues ( $payment, $originalBaseCurrencyCode );
			
					// convert order item base prices
					$orderItems = $order->getAllItems ();
					foreach ( $orderItems as $orderItem ) {
						$helper->convertBaseValues ( $orderItem, $originalBaseCurrencyCode );
					}
			
					// convert order base prices
					$helper->convertBaseValues ( $order, $originalBaseCurrencyCode );
				}
			}			
		}
		
		return $observer;
	}

}

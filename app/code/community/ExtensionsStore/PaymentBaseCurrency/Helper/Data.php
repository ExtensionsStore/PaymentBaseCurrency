<?php
/**
 * 
 *
 * @category   ExtensionsStore
 * @package    ExtensionsStore_PaymentBaseCurrency
 * @author     Extensions Store <admin@extensions-store.com>
 */
class ExtensionsStore_PaymentBaseCurrency_Helper_Data extends Mage_Core_Helper_Abstract {
	protected $_currencyHelper;
	protected $_fromCurrencyCode;
	public function __construct() {
		$this->_currencyHelper = Mage::helper ( 'directory' );
	}
	/**
	 *
	 * @param Mage_Core_Model_Abstract $obj        	
	 * @param $string $fromCurrencyCode        	
	 */
	public function convertBaseValues($obj, $fromCurrencyCode) {
		$this->_fromCurrencyCode = $fromCurrencyCode;
		$data = $obj->getData ();
		foreach ( $data as $key => $value ) {
			if (strpos ( $key, 'base_', 0 ) === 0 && strpos ( $key, '_cost' ) === false && $value && is_numeric ( $value ) && ( float ) $value > 0) {
				$value = $this->convertValue ( $value, $fromCurrencyCode );
				$obj->setData ( $key, $value );
			}
		}
		
		return $obj;
	}
	/**
	 *
	 * @param number $value        	
	 * @param string $fromCurrencyCode        	
	 */
	public function convertValue($value, $fromCurrencyCode = null) {
		if (!$fromCurrencyCode && $this->_fromCurrencyCode){
			$fromCurrencyCode = $this->_fromCurrencyCode;
		}
		$value = $this->_currencyHelper->currencyConvert ( $value, $fromCurrencyCode, MichaelTodd_Authorizenet_Model_Authorizenet::CURRENCY_CODE );
		
		return $value;
	}
}

<?php
namespace ay\Credit_Card;

class Validate /*implements \ArrayAccess*/ {
	private
		$card_number,
		$card_security_code,
		$card_type,
		$expiration_date;
	
	public function __construct ($card_number, $security_code) {
		$this->card_number = (string) $card_number;
		$this->card_security_code = (string) $security_code;
		
		if (!$this->isValidFormat()) {
			throw new Invalid_Card_Exception('Credit Card Number must consist only of digits [0-9].');
		}
		
		if (!$this->isValidChecksum()) {
			throw new Invalid_Card_Exception('Invalid Credit Card Number.');
		}
		
		// @see Visa, MasterCard, American Express (http://www.regular-expressions.info/creditcard.html), Maestro (http://regexlib.com/REDetails.aspx?regexp_id=1626)
		
		$card_pattern = [
			'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
			'mastercard' => '/^5[1-5][0-9]{14}$/',
			'amex' => '/^3[47][0-9]{13}$/',
			'maestro' => '/(^(5[0678])\d{11,18}$) |(^(6[^0357])\d{11,18}$) |(^(601)[^1]\d{9,16}$) |(^(6011)\d{9,11}$) |(^(6011)\d{13,16}$) |(^(65)\d{11,13}$) |(^(65)\d{15,18}$) |(^(633)[^34](\d{9,16}$)) |(^(6333)[0-4](\d{8,10}$)) |(^(6333)[0-4](\d{12}$)) |(^(6333)[0-4](\d{15}$)) |(^(6333)[5-9](\d{8,10}$)) |(^(6333)[5-9](\d{12}$)) |(^(6333)[5-9](\d{15}$)) |(^(6334)[0-4](\d{8,10}$)) |(^(6334)[0-4](\d{12}$)) |(^(6334)[0-4](\d{15}$)) |(^(67)[^(59)](\d{9,16}$)) |(^(6759)](\d{9,11}$)) |(^(6759)](\d{13}$)) |(^(6759)](\d{16}$)) |(^(67)[^(67)](\d{9,16}$)) |(^(6767)](\d{9,11}$)) |(^(6767)](\d{13}$)) |(^(6767)](\d{16}$))/'
		];
	
		foreach ($card_pattern as $type => $pattern) {
			if (preg_match($pattern, $this->card_number)) {
				$this->card_type = $type;
			}
		}
		
		if (!$this->card_type) {
			throw new Invalid_Card_Exception('Invalid credit card number.');
		}
	}
	
	/**
	 * @param int $year
	 * @param int $month
	 */
	public function setExpirationDate ($year, $month) {
		if (!checkdate($month, 1, $year)) {
			throw new Invalid_Card_Exception('Invalid credit card expiration date.');
		} else if (mktime(0, 0, 0, $month, 1, $year) < $_SERVER['REQUEST_TIME']) {
			throw new Invalid_Card_Exception('Expiration date cannot be a past date.');
		}
		
		$this->expiration_date = ['year' => $year, 'month' => $month];
	}
	
	private function isValidFormat () {
		if ($this->card_number != preg_replace('/[^0-9]/', '', $this->card_number)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @see http://en.wikipedia.org/wiki/Luhn_algorithm
	 * @return boolean
	 */
	private function isValidChecksum () {
		$card_number_checksum = '';
		
		foreach (str_split(strrev($this->card_number)) as $i => $d) {
			$card_number_checksum .= $i %2 !== 0 ? $d * 2 : $d;
		}
		
		return array_sum(str_split($card_number_checksum)) % 10 === 0;
	}
		
	public function get () {
		return [
			'number' => $this->card_number,
			'security_code' => $this->card_security_code,
			'type' => $this->card_type,
			'expiration_date' => $this->expiration_date
		];
	}
}
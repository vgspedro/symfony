<?php
namespace App\Service;

use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;

class MoneyParser
{
	private $decimalMoneyParser;
	
	public function __construct()
	{		
		$this->decimalMoneyParser = new DecimalMoneyParser(new ISOCurrencies()); 
	}
	
	public function parse($value, $currency = 'EUR')
	{
		return $this->decimalMoneyParser->parse($value, $currency);
	}	
}

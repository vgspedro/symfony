<?php
namespace App\Service;

use Money\Currencies\ISOCurrencies; 
use Money\Formatter\DecimalMoneyFormatter;

class MoneyFormatter
{
	private $decimalMoneyFormatter;
	
	public function __construct()
	{	
		$this->decimalMoneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());		
	}
	
	public function format($value)
	{
		return $this->decimalMoneyFormatter->format($value);
	}
}

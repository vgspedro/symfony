<?php
namespace App\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Money\Money;

class MoneyType extends Type
{
	const MONEY = 'money';
	
	public function getName()
	{
		return self::MONEY;
	}
	
	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return 'INT(11) UNSIGNED COMMENT "(DC2Type:money)"';
	}
	
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{		
		return Money::EUR($value);
	}
	
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value instanceof Money) {
			$value = $value->getAmount();
		}
		
		return $value;
	}	
}

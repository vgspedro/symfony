<?php
namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountInactiveException extends AccountStatusException
{
	public function getMessageKey()
	{
		return 'Account is inactive.';
	}
}

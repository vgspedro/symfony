<?php
namespace App\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use App\Entity\Operator;

class ApiKeyOperatorProvider implements UserProviderInterface
{
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	public function getUsernameForApiKey($apiKey)
	{
		
		return $username;
	}
	
	public function loadUserByUsername($username)
	{
		$repository = $this->entityManager->getRepository(Operator::class);
		
		$operator = $repository->find($username);
		if (!$operator) {
			//throw new 	
		}
		
		return;
	}
	
	public function refreshUser(UserInterface $user)
	{
		throw new UnsupportedUserException();
	}
	
	public function supportsClass($class)
	{
		return Operator::class === $class;
	}
}

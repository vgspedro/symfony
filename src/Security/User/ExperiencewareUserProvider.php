<?php
namespace App\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ExperiencewareUserProvider implements UserProviderInterface
{
	public function loadUserByUsername($username)
	{			
		return new \App\Entity\Operator($username, '$2y$13$NlHIoaR7eBW4ow7nIRuEde/0x6oregsyBRYto0FcjjMFdJdeWrCoi', null, 'ROLE_ADMIN');
		
	}
	
	public function refreshUser(UserInterface $user)
	{
		if (!$user instanceof User) {
			throw new UnsupportedUserException(
					sprintf('Instances of "%s" are not supported.', get_class($user))
			);
		}
		
		return $this->loadUserByUsername($user->getUsername());
	}
	
	public function supportsClass($class)
	{
		return User::class === $class;
	}	
}


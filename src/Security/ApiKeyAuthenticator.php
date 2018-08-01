<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
	public function supports(Request $request)
	{
		return $request->request->has('id') && $request->request->has('ticket');
	}
	
	public function getCredentials(Request $request)
	{
		return array('id' => $request->request->get('id'));
	}
	
	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		$apiKey = $credentials['id'];
		
		if ($apiKey === null) {
			return;
		}
		
		return $userProvider->loadUserByUsername(hexdec($apiKey));
	}
	
	public function checkCredentials($credentials, UserInterface $user)
	{
		return true;		
	}
	
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
	{
		return null;
	}
	
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		$data = array('message' => strtr($exception->getMessage(), $exception->getMessageData()));
		
		return new JsonResponse($data, Response::HTTP_FORBIDDEN);
	}
	
	public function start(Request $request, AuthenticationException $authException = null)
	{
		$data = array('message' => 'Authentication Required');
		
		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}
	
	public function supportsRememberMe()
	{
		return false;
	}
}

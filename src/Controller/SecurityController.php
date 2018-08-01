<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController

{

    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
    // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login-users.html', array(
        'last_username' => $lastUsername,
        'error'         => $error,
        ));
    }
   

    public function loginClients(Request $request, AuthenticationUtils $authenticationUtils)
    {
    // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login-clients.html', array(
        'last_username' => $lastUsername,
        'error'         => $error,
        ));
    }


}

?>

<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Company;

class SecurityController extends AbstractController

{

    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
    // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);

        return $this->render('security/login-users.html', array(
        'last_username' => $lastUsername,
        'error'         => $error,
        'company' => $company,
        ));
    }
   

    public function loginClients(Request $request, AuthenticationUtils $authenticationUtils)
    {
    // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);
        
        return $this->render('security/login-clients.html', array(
        'last_username' => $lastUsername,
        'error'         => $error,
        'company' => $company,
        ));
    }


}

?>

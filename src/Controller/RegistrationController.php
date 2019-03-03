<?php
namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $transport = (new \Swift_SmtpTransport($_ENV['EMAIL_SMTP'], $_ENV['EMAIL_PORT'], $_ENV['EMAIL_CERTIFICADE']))
            ->setUsername($_ENV['EMAIL'])
            ->setPassword($_ENV['EMAIL_PASS']);    

            $mailer = new \Swift_Mailer($transport);
                    
            $subject ='Registo efetuado';


            $message = (new \Swift_Message($subject))
                ->setFrom([$_ENV['EMAIL'] => $_ENV['EMAIL_USERNAME']])
                ->setTo([$user->getEmail() => $user->getUsername(), $_ENV['EMAIL'] => $_ENV['EMAIL_USERNAME'] ])
                ->addPart($subject, 'text/plain')
                ->setBody(
                    $this->renderView(
                        'emails/register.html.twig',
                        array(
                            'username' => $user->getUsername(),
                            'logo' => '/images/logo.png'
                        )
                    ),
                'text/html'
            );

            $mailer->send($message);

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'admin/register-new.html',
            array('form' => $form->createView())
        );
    }
}
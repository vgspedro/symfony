<?php
namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\DBAL\DBALException;
use App\Entity\Company;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrationController extends AbstractController
{

    public function userNew(/*Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer*/)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        return $this->render(
            'admin/register-new.html',
            array('form' => $form->createView())
        );
    }


    public function userCreate(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            try {

                // 4) save the User!
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                
                $company = $em->getRepository(Company::class)->find(1);

                $transport = (new \Swift_SmtpTransport($_ENV['EMAIL_SMTP'], $_ENV['EMAIL_PORT'], $_ENV['EMAIL_CERTIFICADE']))
                    ->setUsername($company->getEmail())
                    ->setPassword($company->getEmailPass());       

                $mailer = new \Swift_Mailer($transport);
                        
                $subject ='Registo efetuado';

                $message = (new \Swift_Message($subject))
                    ->setFrom([$company->getEmail() => $company->getName()])
                    ->setTo([$user->getEmail() => $user->getUsername(), $company->getEmail() => $company->getName()])
                    ->addPart($subject, 'text/plain')
                    ->setBody(
                        $this->renderView(
                            'emails/register.html.twig',
                            array(
                                'username' => $user->getUsername(),
                                'logo' => '/upload/gallery/'.$company->getLogo()
                            )
                        ),
                    'text/html'
                );

                $mailer->send($message);

                return new JsonResponse(array('status' => 1, 'message' => 'criado com sucesso', 'data' => null));
            }

            catch(DBALException $e){

                if (preg_match("/'password'/i", $e))
                    $a = array( "Insira Password.");
                else if (preg_match("/'password_repeat'/i", $e))
                    $a = array("As passwords tem que ser iguais.");
                else
                    $a = array("Contate administrador sistema sobre: ".$e->getMessage());
                
                return new JsonResponse(array(
                    'status' => 3,
                    'message' => 'fail',
                    'data' => $a));
            }
        }

        else{   
            return new JsonResponse(array(
                'status' => 4,
                'message' => 'fail',
                'data' => $this->getErrorMessages($form)));
        }

        return new JsonResponse(array(
            'status' => 2,
            'message' => 'fail not submitted',
           'data' => null));
    }



    protected function getErrorMessages(\Symfony\Component\Form\Form $form) 
    {
        $errors = array();
        $err = array();
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors [] = $this->getErrorMessages($child);
            }
        }

        foreach ($errors as $error) {
            if ($error == 'Email already taken')
                $err [] = 'Email já existente';
            else if ($error == 'Username already taken')
                $err [] = 'Nome já existente';
            else if ($error == 'Este valor não é válido.')
                $err [] = 'Ambos os campos Password tem que ser iguais';
            else 
                $err [] = $error;
        }

        return $err;
    }


}
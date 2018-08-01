<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Form\ChangePasswordType;

class UserController extends AbstractController
{
    public function listUser(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/app-users.html', array(
            'users' =>  $users));
    }

    public function statusUser (Request $request){
        
        $id = $request->request->get('id');
        $status = $request->request->get('status');
        
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            $reponse = array('message' => 'fail', 'data' =>'Utilizador Não encontrado ', 'request'=>'');
        }    
        else{
            $user->setStatus($status);
            $em->flush();

            $response = array('message' => 'success', 'data' => $user->getStatus(), 'request '=> $status);
        }

        return new JsonResponse($response);
    }

    public function deleteUser(Request $request){

        $response = array();
        
        $id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            $response = array('message'=>'fail', 'data' => 'Utlizador #'.$id.' não existe!', 'request' => $id);
        }
        else{
            $em->remove($user);
            $em->flush();
            $response = array('message'=>'success', 'data' => $user->getId(), 'request' => $id);
        }

        return new JsonResponse($response);

    }


    public function passwordUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){

        $em = $this->getDoctrine()->getManager();

        if($request->query->get('id')){
            $id = $request->query->get('id');
            $user = $em->getRepository(User::class)->find($id);

            $passwordForm = $this->createForm(ChangePasswordType::class, $user);
            
            return $this->render('admin/user-password.html', array(
                'passwordForm' => $passwordForm->createView(),
                //'editFormErrors' => true
            ));
        }
        
        
        else{

            $id = $request->request->get('id');

            $user = $em->getRepository(User::class)->find($id);
        
            $form = $this->createForm(ChangePasswordType::class, $user);
        
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {

                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

                $user->setPassword($password);
                $em->persist($user);
                $em->flush();

                $response = array('message'=>'success', 'data' => $user->getId(), 'request' => $id);      
            } 
            else {

                $errorMessages = array();

                foreach ($form['plainPassword']->getErrors(true) as $error) {
                    $errorMessages [] = $error->getMessage();
                }           
              
                $response = array('message'=>'fail', 'data' => $errorMessages, 'request' => $id);
            }

            return new JsonResponse($response);
        }

    }

}

?>
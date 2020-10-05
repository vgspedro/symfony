<?php
namespace App\Controller;

use App\Entity\Promocode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\PromocodeType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;

class PromocodeController extends AbstractController
{

    public function index(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        $promocodes = $em->getRepository(Promocode::class)->findAll();
        $form = $this->createForm(PromocodeType::class);
        return $this->render('admin/promocode-list.html', [
            'form' => $form->createView(),
            'promocodes' => $promocodes
        ]);
    }

    public function aboutUsEdit(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        
        $aboutUs = $em->getRepository(AboutUs::class)->find($request->request->get('id'));

        $form = $this->createForm(AboutUsType::class, $aboutUs);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 

            $aboutUs = $form->getData();

                try {
                    $em->persist($aboutUs);
                    $em->flush();

                    $response = array(
                        'status' => 1,
                        'message' => 'Sucesso',
                        'data' => 'O registo '.$aboutUs->getId().' foi gravado.');
                } 
                catch(DBALException $e){

                    $a = array("Contate administrador sistema sobre: ".$e->getMessage());

                    $response = array(
                        'status' => 0,
                        'message' => 'fail',
                        'data' => $a);
                }
            }
            
            else{   
                $response = array(
                    'result' => 0,
                    'message' => 'fail',
                    'data' => $this->getErrorMessages($form)
                );
            }
        }
        return new JsonResponse($response);
    }


    public function aboutUsDelete(Request $request){

        $response = array();
        $aboutUsId = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        
        $aboutUs = $em->getRepository(AboutUs::class)->find($aboutUsId);
       
        if (!$aboutUs) {
            $response = array('message'=>'fail', 'status' => 'Registo #'.$aboutUsId . ' não existe.');
        }
        else{
            $em->remove($aboutUs);
            $em->flush();

            $response = array('message'=>'success', 'status' => $aboutUsId);
        }
        return new JsonResponse($response);
    }


    protected function getErrorMessages(\Symfony\Component\Form\Form $form) 
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors [] = $this->getErrorMessages($child);
            }
        }
        return $errors;
    }

}

?>
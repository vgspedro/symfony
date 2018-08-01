<?php
namespace App\Controller;

use App\Entity\EasyText;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\EasyTextType;

class EasyTextController extends AbstractController
{
    public function EasyText(Request $request, ValidatorInterface $validator)
    {
       
        $em = $this->getDoctrine()->getManager();

        //check if id is in request is so update else create new one.

        if($request->request->get('id')){
            $easyText = $em->getRepository(EasyText::class)->find($request->request->get('id'));
            $form = $this->createForm(EasyTextType::class, $easyText);
        }
        else
        {
            $easyText = $em->getRepository(EasyText::class)->findAll();
            $form = $this->createForm(EasyTextType::class);
        } 
        
        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){
                
                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();
                    $easyTexts = $form->getData();
                    $em->persist($easyTexts);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $easyTexts->getId());
                }
                else{   
                    $response = array(
                        'result' => 0,
                        'message' => 'fail',
                        'data' => $this->getErrorMessages($form)
                    );
                }
            }
            else
                $response = array(
                    'result' => 2,
                    'message' => 'fail not submitted',
                    'data' => '');
                return new JsonResponse($response);
        }

        return $this->render('admin/easy-text.html',array(
            'form' => $form->createView(),
            'easyTexts' => $easyText
        ));
    }


    public function EasyTextDelete(Request $request){

        $response = array();
        $easyTextId = $request->request->get('id');
        $entity = $this->getDoctrine()->getManager();
        
        $easyText = $entity->getRepository(EasyText::class)->find($easyTextId);
       
        if (!$easyText) {
            $response = array('message'=>'fail', 'status' => 'Texto Fácil #'.$easyTextId . ' não existe.');
        }
        else{
            $entity->remove($easyText);
            $entity->flush();
            $response = array('message'=>'success', 'status' => $easyTextId);
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
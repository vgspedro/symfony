<?php
namespace App\Controller;

use App\Entity\Rgpd;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\RgpdType;

class RgpdController extends AbstractController
{
    public function rgpd(Request $request, ValidatorInterface $validator)
    {
       
        $em = $this->getDoctrine()->getManager();

        //check if id is in request is so update else create new one.

        if($request->request->get('id')){
            $rgpd = $em->getRepository(Rgpd::class)->find($request->request->get('id'));
            $form = $this->createForm(RgpdType::class, $rgpd);
        }
        else
        {
            $rgpd = $em->getRepository(Rgpd::class)->findAll();
            $form = $this->createForm(RgpdType::class);
        } 
        
        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){
                
                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();
                    $rgpds = $form->getData();
                    $em->persist($rgpds);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $rgpds->getId());
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

        return $this->render('admin/rgpd.html',array(
            'form' => $form->createView(),
            'rgpd' => $rgpd
        ));

        return $this->render('admin/rgpd.html');
    }


    public function rgpdDelete(Request $request){

        $response = array();
        $rgpdId = $request->request->get('id');
        $entity = $this->getDoctrine()->getManager();
        
        $rgpd = $entity->getRepository(Rgpd::class)->find($rgpdId);
       
        if (!$rgpd) {
            $response = array('message'=>'fail', 'status' => 'Rgpd #'.$rgpdId . ' não existe.');
        }
        else{
            $entity->remove($rgpd);
            $entity->flush();
            $response = array('message'=>'success', 'status' => $rgpdId);
        }
        return new JsonResponse($response);
    }


    public function rgpdShow(Request $request){

        $response = array();
        $rgpdId = $request->request->get('rgpdId');
        $entity = $this->getDoctrine()->getManager();
        
        $rgpd = $entity->getRepository(Rgpd::class)->find($rgpdId);
       
        if (!$rgpd) {
            $response = array('message'=>'fail', 'status' => 'Rgpd #'.$rgpdId . ' não existe.');
        }
        else{
            $response = array('message'=>'success', 'name' => $rgpd->getName(), 'rgpd' => $rgpd->getRgpdHtml());
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
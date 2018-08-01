<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Blockdates;
use App\Entity\Event;
use App\Entity\Category;
use App\Form\BlockdatesType;

class BlockDateController extends AbstractController
{


    public function adminBlockDateEdit(Request $request)
    {
        $form = $this->createForm(BlockdatesType::class);

        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){
                
                if($form->isValid()){ 

                    $em = $this->getDoctrine()->getManager();
                    
                    /*EDIT ONE*/
                    if($request->request->get('blockdates')['category']){
                        $category = $em->getRepository(Blockdates::class)->findOneBy(['category' => $request->request->get('blockdates')['category']]);
                        $blockdates = $em->getRepository(Blockdates::class)->find($category->getId());
                        $blockdates->setDate($request->request->get('blockdates')['date']);
                        $blockdates->setOnlyDates($request->request->get('blockdates')['onlydates']);
                        $em->flush();            
                    }
                    /*EDIT ALL*/
                    else{
                        foreach ($em->getRepository(Blockdates::class)->findAll() as $blockdates) {
                            $blockdates->setDate($request->request->get('blockdates')['date']);
                            $blockdates->setOnlyDates($request->request->get('blockdates')['onlydates']);
                        }
                        $em->flush();
                    } 

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $blockdates->getId());
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
        return $this->render('admin/block-date-edit.html',array(
            'form' => $form->createView()));
    }



    /*get blocked dates, type blocked of current category edit admin*/
    public function adminBlockDateSetValues(Request $request)
    {
        $categoryId = $request->request->get('id');
        $blockedDates = $this->getDoctrine()->getRepository(Blockdates::class)
        ->findOneBy(['category' => $categoryId]);
        
        $response = !$blockedDates ? 
            array('data' => 'fail', 'result' => 0, 'type' => $blockedDates->getOnlyDates()) : 
            array('data' => 'success', 'result' => $blockedDates->getDate(), 'type' => $blockedDates->getOnlyDates());

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
<?php
namespace App\Controller;

use App\Entity\Warning;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\WarningType;

class WarningController extends AbstractController
{
        public function adminWarning(Request $request, ValidatorInterface $validator)
    {
        $id = 10;/*this is update only*/

        $em = $this->getDoctrine()->getManager();       
        $warning = $em->getRepository(Warning::class)->find($id);
        if (!$warning) {
            throw $this->createNotFoundException('db no id 1 warning', array('%id%' => $id));
        }                        
        $form = $this->createForm(WarningType::class, $warning);

        return $this->render('admin/warning.html',array(
        'form' => $form->createView()));
    }


    public function adminWarningEdit(Request $request, ValidatorInterface $validator)
    {
        $id = 10;/*this is update only*/

        $em = $this->getDoctrine()->getManager();       
        $warning = $em->getRepository(Warning::class)->find($id);
        if (!$warning) {
            throw $this->createNotFoundException('db no id 1 warning', array('%id%' => $id));
        }                        
        $form = $this->createForm(WarningType::class, $warning);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $warning = $form->getData();

                $em->persist($warning);
                $em->flush();
                $response = array(
                    'result' => 1,
                    'message' => 'success',
                    'data' => $warning->getId());
            }
            else{   
                $response = array(
                    'result' => 0,
                    'message' => 'fail',
                    'data' => $this->getErrorMessages($form)
                );
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
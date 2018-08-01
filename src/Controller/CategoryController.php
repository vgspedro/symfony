<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\CategoryType;

class CategoryController extends AbstractController
{

    public function adminCategoryNew(Request $request, ValidatorInterface $validator)
    {
        $validate = new Category();

        $form = $this->createForm(CategoryType::class, $validate);

        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){
                
                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();
                    $category = $form->getData();
                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $category->getId());
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
        return $this->render('admin/category-new.html',array(
            'form' => $form->createView()));
    }


    public function adminCategoryList(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository(Event::class)->findAll();

        return $this->render('admin/category-list.html',array(
            'categories' =>  $events));
    }


    public function adminCategoryEdit(Request $request, ValidatorInterface $validator)
    {

        $categoryId = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($categoryId);

        $form = $this->createForm(CategoryType::class, $category);

           if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){
                
                if($form->isValid()){ 

                    $em = $this->getDoctrine()->getManager();
                    $category = $form->getData();
                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $category->getId());
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


        return $this->render('admin/category-edit.html',array(
            'form' => $form->createView()));
    }

 public function adminCategoryDelete(Request $request)
    {
        $response = array();
        $categoryId = $request->request->get('id');
        $entity = $this->getDoctrine()->getManager();
        
        $event = $entity->getRepository(Event::class)->findOneBy(['category' => $categoryId]);
        $blocked = $entity->getRepository(Blockdates::class)->findOneBy(['category' => $categoryId]);
        $category = $entity->getRepository(Category::class)->find($categoryId);
       
        if (!$category || !$event|| !$blocked) {
            $response = array('message'=>'fail', 'status' => 'Categoria #'.$categoryId . ' não existe.');
        }
        else{
            $entity->remove($blocked);
            $entity->flush();
            $entity->remove($event);
            $entity->flush();
            $entity->remove($category);
            $entity->flush();
            $response = array('message'=>'success', 'status' => $categoryId);
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
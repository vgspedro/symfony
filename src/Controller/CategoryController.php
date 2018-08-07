<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\BlockDates;
use App\Entity\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Form\CategoryType;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;



class CategoryController extends AbstractController
{

    private $categories_images_directory;
    
    public function __construct($categories_images_directory)
    {
        $this->categories_images_directory = $categories_images_directory;
    }
    


    public function categoryNew(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        return $this->render('admin/category-new.html',array(
            'form' => $form->createView()));
    }


    public function categoryAdd(Request $request, ValidatorInterface $validator, FileUploader $fileUploader,ImageResizer $imageResizer)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

            if($form->isSubmitted()){
                
                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();

                    $file = $category->getImage();

                    if ($file) {
                        $fileName = $fileUploader->upload($file);               
                        $imageResizer->resize($fileName);
                        $category->setImage($fileName);
                    }
                    else{
                        $category->setImage($this->categories_images_directory.'/no-image.png');
                    }

                try {

                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $category->getId());
                    } 
                    catch(DBALException $e){

                        if (preg_match("/'event'/i", $e))
                            $a = array( "Insira pelo menos 1 hora.");

                        else if (preg_match("/'children_price'/i", $e))
                            $a = array("Preço Criança (€)* não pode ser vazio, insira 0 ou maior.");

                        else
                            $a = array("Contate administrador sistema sobre: ".$e->getMessage());

                        $response = array(
                            'result' => 0,
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
            else
                $response = array(
                    'result' => 2,
                    'message' => 'fail not submitted',
                    'data' => '');

        return new JsonResponse($response);
    }



    public function categoryList(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository(Event::class)->findAll();

        return $this->render('admin/category-list.html',array(
            'categories' =>  $events));
    }



    public function categoryShowEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {

        $categoryId = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($categoryId);

        if ($category->getImage()) {

            $path = file_exists($this->categories_images_directory.'/'.$category->getImage()) ?
                $this->categories_images_directory.'/'.$category->getImage()
                :
                $this->categories_images_directory.'/no-image.png';

            $category->setImage(new File($path));
        }

        else
            $category->setImage(new File($this->categories_images_directory.'/no-image.png'));

        $form = $this->createForm(CategoryType::class, $category);

        return $this->render('admin/category-edit.html',array(
            'form' => $form->createView(),
            'category' => $category
        ));
    }




    public function categoryEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {

        $categoryId = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($categoryId);

        $img = $category->getImage();

        if ($category->getImage()) {
            
            $path = file_exists($this->categories_images_directory.'/'.$category->getImage()) ?

            $this->categories_images_directory.'/'.$category->getImage()
            :
            $this->categories_images_directory.'/no-image.png';

            $category->setImage(new File($path));
        }

        else

            $category->setImage(new File($this->categories_images_directory.'/no-image.png'));

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 
                
                $deleted = 1;
                $category = $form->getData();
                $file = $category->getImage();

                if (is_object($file)) {
                    $fileName = $fileUploader->upload($file);               
                    $imageResizer->resize($fileName);
                    $category->setImage($fileName);
                    
                    //remove from folder older image

                    $fileSystem = new Filesystem();

                    if ($img && $img != 'no-image.png') {
                        try {
                            $fileSystem->remove($this->categories_images_directory.'/'.$img);
                        } 
                        catch (IOExceptionInterface $exception) {
                            $deleted = '0 '.$exception->getPath();
                        }
                    }
                }
                else
                    $category->setImage($img);

                try {
                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'image' => $deleted,
                        'data' => $category->getId());
                } 
                catch(DBALException $e){

                    if (preg_match("/'event'/i", $e))
                        $a = array( "Insira pelo menos 1 hora.");

                    else if (preg_match("/'children_price'/i", $e))
                        $a = array("Preço Criança (€)* não pode ser vazio, insira 0 ou maior.");

                    else
                        $a = array("Contate administrador sistema sobre: ".$e->getMessage());

                    $response = array(
                        'result' => 0,
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

    public function categoryDelete(Request $request)
    {
        $deleted = 1;
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

            $img = $category->getImage();

            $entity->remove($blocked);
            $entity->flush();
            $entity->remove($event);
            $entity->flush();
            $entity->remove($category);
            $entity->flush();

            //remove from folder image

            $fileSystem = new Filesystem();

            if ($img && $img != 'no-image.png') {
                try {
                    $fileSystem->remove($this->categories_images_directory.'/'.$img);
                } 
                catch (IOExceptionInterface $exception) {
                    $deleted = '0 '.$exception->getPath();
                }
            }
            
            $response = array('message'=>'success', 'image' => $deleted, 'status' => $categoryId);
        }
        return new JsonResponse($response);
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
            if ($error == 'NAME_PT')
                $err [] = 'Nome (PT)*';
            else if ($error == 'NAME_EN')
                $err [] = 'Nome (EN)*';
            else if ($error == 'DESCRIPTION_PT')
                $err [] = 'Descrição (PT)*';
            else if ($error == 'DESCRIPTION_EN')
                $err [] = 'Descrição (EN)*';
            else if ($error == 'ADULT_PRICE')
                $err [] = 'Preço Adulto (€)*';
            else if ($error == 'CHILDREN_PRICE')
                $err [] = 'Preço Criança (€)*';
            else 
                $err [] = $error;
        }

        return $err;
    }
}

?>
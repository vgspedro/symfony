<?php
namespace App\Controller;

use App\Entity\Gallery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Form\GalleryType;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;


class GalleryController extends AbstractController
{

    private $gallery_images_directory;
    
    public function __construct($gallery_images_directory)
    {
        $this->gallery_images_directory = $gallery_images_directory;
    }

    public function galleryNew(Request $request)
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);
        return $this->render('admin/gallery-new.html',array(
            'form' => $form->createView()));
    }


    public function galleryAdd(Request $request, ValidatorInterface $validator, FileUploader $fileUploader,ImageResizer $imageResizer)
    {
        $gallery = new Gallery();

        $form = $this->createForm(GalleryType::class, $gallery);

        $form->handleRequest($request);

            if($form->isSubmitted()){
                
                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();

                    $file = $gallery->getImage();

                    if ($file) {
                        $fileName = $fileUploader->upload($file);               
                        $imageResizer->resize($fileName);
                        $gallery->setImage($fileName);
                    }
                    else{
                        $gallery->setImage($this->gallery_images_directory.'/no-image.png');
                    }

                try {

                    $em->persist($gallery);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $gallery->getId());
                    } 
                    catch(DBALException $e){

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



    public function galleryList(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $gallery = $em->getRepository(Gallery::class)->findAll();

        return $this->render('admin/gallery-list.html',array(
            'galleries' =>  $gallery));
    }



    public function galleryShowEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {

        $galleryId = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $gallery = $em->getRepository(Gallery::class)->find($galleryId);

        if ($gallery->getImage()) {

            $path = file_exists($this->gallery_images_directory.'/'.$gallery->getImage()) ?
                $this->gallery_images_directory.'/'.$gallery->getImage()
                :
                $this->gallery_images_directory.'/no-image.png';

            $gallery->setImage(new File($path));
        }

        else
            $gallery->setImage(new File($this->gallery_images_directory.'/no-image.png'));

        $form = $this->createForm(GalleryType::class, $gallery);

        return $this->render('admin/gallery-edit.html',array(
            'form' => $form->createView(),
            'gallery' => $gallery
        ));
    }




    public function galleryEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {

        $galleryId = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $gallery = $em->getRepository(Gallery::class)->find($galleryId);

        $img = $gallery->getImage();

        if ($gallery->getImage()) {
            
            $path = file_exists($this->gallery_images_directory.'/'.$gallery->getImage()) ?

            $this->gallery_images_directory.'/'.$gallery->getImage()
            :
            $this->gallery_images_directory.'/no-image.png';

            $gallery->setImage(new File($path));
        }

        else

            $gallery->setImage(new File($this->gallery_images_directory.'/no-image.png'));

        $form = $this->createForm(GalleryType::class, $gallery);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 
                
                $deleted = 1;
                $gallery = $form->getData();
                $file = $gallery->getImage();

                if (is_object($file)) {
                    $fileName = $fileUploader->upload($file);               
                    $imageResizer->resize($fileName);
                    $gallery->setImage($fileName);
                    
                    //remove from folder older image

                    $fileSystem = new Filesystem();

                    if ($img && $img != 'no-image.png') {
                        try {
                            $fileSystem->remove($this->gallery_images_directory.'/'.$img);
                        } 
                        catch (IOExceptionInterface $exception) {
                            $deleted = '0 '.$exception->getPath();
                        }
                    }
                }
                else
                    $gallery->setImage($img);

                try {
                    $em->persist($gallery);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'image' => $deleted,
                        'data' => $gallery->getId());
                } 
                catch(DBALException $e){

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

    public function galleryDelete(Request $request)
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
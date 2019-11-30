<?php
namespace App\Controller;

use App\Entity\Staff;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Form\StaffType;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;


class StaffController extends AbstractController
{

    private $staff_images_directory;
    
    public function __construct($staff_images_directory)
    {
        $this->staff_images_directory = $staff_images_directory;
    }

    public function staffNew(Request $request)
    {
        $staff = new Staff();
     
        $form = $this->createForm(StaffType::class, $staff);
      
        $form->handleRequest($request);

        return $this->render('admin/staff-new.html',array(
            'form' => $form->createView()));
    }

    public function staffAdd(Request $request, ValidatorInterface $validator, FileUploader $fileUploader,ImageResizer $imageResizer)
    {
        $staff = new Staff();

        $form = $this->createForm(StaffType::class, $staff);

        $form->handleRequest($request);

            if($form->isSubmitted()){
                
                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();

                    $file = $staff->getImage();

                    if ($file) {
                        $fileName = $fileUploader->upload($file);               
                        $imageResizer->resize($fileName);
                        $staff->setImage($fileName);
                    }
                    else{
                        $staff->setImage($this->staff_images_directory.'/no-image.png');
                    }

                try {

                    $em->persist($staff);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $staff->getId());
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



    public function staffList(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $staffs = $em->getRepository(Staff::class)->findAll();

        return $this->render('admin/staff-list.html', 
            ['staffs' =>  $staffs]);
    }


    public function staffShowEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {

        $staff_id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $staff = $em->getRepository(Staff::class)->find($staff_id);

        if ($staff->getImage()) {

            $path = file_exists($this->staff_images_directory.'/'.$staff->getImage()) ?
                $this->staff_images_directory.'/'.$staff->getImage()
                :
                $this->staff_images_directory.'/no-image.png';

            $staff->setImage(new File($path));
        }

        else
            $staff->setImage(new File($this->staff_images_directory.'/no-image.png'));

        $form = $this->createForm(StaffType::class, $staff);

        return $this->render('admin/staff-edit.html',array(
            'form' => $form->createView(),
            'staff' => $staff
        ));
    }


    public function staffEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {

        $staff_id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $staff = $em->getRepository(Staff::class)->find($staff_id);

        $img = $staff->getImage();

        if ($staff->getImage()) {
            
            $path = file_exists($this->staff_images_directory.'/'.$staff->getImage()) ?

            $this->staff_images_directory.'/'.$staff->getImage()
            :
            $this->staff_images_directory.'/no-image.png';

            $staff->setImage(new File($path));
        }

        else

            $staff->setImage(new File($this->staff_images_directory.'/no-image.png'));

        $form = $this->createForm(StaffType::class, $staff);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 
                
                $deleted = 1;
                $staff = $form->getData();
                $file = $staff->getImage();

                if (is_object($file)) {
                    $fileName = $fileUploader->upload($file);               
                    $imageResizer->resize($fileName);
                    $staff->setImage($fileName);
                    
                    //remove from folder older image

                    $fileSystem = new Filesystem();

                    if ($img && $img != 'no-image.png') {
                        try {
                            $fileSystem->remove($this->staff_images_directory.'/'.$img);
                        } 
                        catch (IOExceptionInterface $exception) {
                            $deleted = '0 '.$exception->getPath();
                        }
                    }
                }
                else
                    $staff->setImage($img);

                try {
                    $em->persist($staff);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'image' => $deleted,
                        'data' => $staff->getId());
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

    public function staffDelete(Request $request)
    {
        $deleted = 1;
        $response = array();
        $staff_id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();

        $staff= $em->getRepository(Staff::class)->find($staff_id);
       
        if (!$styaff) {
            $response = ['status' => 0, 'message' => 'Staff #'.$staff_id .' não existe.', 'data' => null];
        }
        else{
            $img = $staff->getImage();
            $em->remove($staff);
            $em->flush();

            //remove from folder image

            $fileSystem = new Filesystem();

            if ($img && $img != 'no-image.png') {
                try {
                    $fileSystem->remove($this->staff_images_directory.'/'.$img);
                } 
                catch (IOExceptionInterface $exception) {
                    $deleted = '0 '.$exception->getPath();
                }
            }
            
            $response = array('status'=> 1, 'data' => $deleted, 'message' => $staff_id);
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
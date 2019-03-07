<?php
namespace App\Controller;

use App\Entity\Company;
use App\Entity\Locales;
use App\Entity\CompanyTranslation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Form\CompanyType;
use App\Form\CompanyTranslationType;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;
use App\Service\MoneyFormatter;


class CompanyController extends AbstractController
{


    private $gallery_images_directory;

    public function __construct($gallery_images_directory)
    {
        $this->gallery_images_directory = $gallery_images_directory;
    }
    
    public function companyNew(Request $request)
    {
        $company = new Company();
        $em = $this->getDoctrine()->getManager();
        $locales = $em->getRepository(Locales::class)->findAll();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        return $this->render('admin/company-new.html',array(
            'form' => $form->createView(),'locales'=>$locales));
    }

    public function companyAdd(Request $request, ValidatorInterface $validator, FileUploader $fileUploader,ImageResizer $imageResizer)
    {
        $company = new Company();
        
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CompanyType::class, $company);

        $form->handleRequest($request);

            if($form->isSubmitted()){
                
                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();

                    $file = $company->getLogo();

                    if ($file) {
                        $fileName = $fileUploader->upload($file);               
                        $imageResizer->resize($fileName);
                        $company->setLogo($fileName);
                    }
                    else{
                        $company->setLogo($this->gallery_images_directory.'/no-image.png');
                    }

                try {
                    
                    $em->persist($company);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $company->getId());
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



    public function companyShowEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        if ($company->getLogo()) {

            $path = file_exists($this->gallery_images_directory.'/'.$company->getLogo()) ?
                $this->gallery_images_directory.'/'.$company->getLogo()
                :
                $this->gallery_images_directory.'/no-image.png';

            $company->setLogo(new File($path));
        }

        else
            $company->setLogo(new File($this->gallery_images_directory.'/no-image.png'));

        $form = $this->createForm(CompanyType::class, $company);

        return $this->render('admin/company-edit.html',array(
            'form' => $form->createView(),
            'company' => $company
        ));
    }



    public function companyEdit(Request $request, ValidatorInterface $validator, FileUploader $fileUploader, ImageResizer $imageResizer)
    {
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $img = $company->getLogo();

        if ($company->getLogo()) {
            
            $path = file_exists($this->gallery_images_directory.'/'.$company->getLogo()) ?

            $this->gallery_images_directory.'/'.$company->getLogo()
            :
            $this->gallery_images_directory.'/no-image.png';

            $company->setLogo(new File($path));
        }

        else

            $company->setLogo(new File($this->gallery_images_directory.'/no-image.png'));

        $form = $this->createForm(CompanyType::class, $company);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 
                
                $deleted = 1;
                $company = $form->getData();
                $file = $company->getLogo();

                if (is_object($file)) {
                    $fileName = $fileUploader->upload($file);               
                    $imageResizer->resize($fileName);
                    $company->setLogo($fileName);
                    
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
                    $company->setLogo($img);
                try {
                    $em->persist($company);
                    $em->flush();

                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'image' => $deleted,
                        'data' => $company->getId());
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
                    'status' => 0,
                    'message' => 'fail',
                    'data' => $this->getErrorMessages($form)
                );
            }
        }
        return new JsonResponse($response);
    }


    public function companyList(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);
   
        return $this->render('admin/company-list.html', array(
            'company' =>  $company));
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
                $err [] = $error;
        }

        return $err;
    }
}

?>
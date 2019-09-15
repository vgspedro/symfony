<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Booking;
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
use App\Service\MoneyFormatter;
use Money\Money;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Stripe;

class CategoryController extends AbstractController
{

    private $categories_images_directory;
    
    public function __construct($categories_images_directory)
    {
        $this->categories_images_directory = $categories_images_directory;
    }
    

    public function categoryPayment(Request $request, Stripe $stripe,TranslatorInterface $translator)
    {

        $em = $this->getDoctrine()->getManager();
        
        $id = $request->request->get('category');

        $category = $em->getRepository(Category::class)->find($id);

        if(!$category)
            return new JsonResponse([
                'status' => 0,
                'message' => 'fail',
                'data' => 'Product not found??'

            ]);

        if(!$category->getWarrantyPayment())
            return new JsonResponse([
                'status' => 1,
                'message' => 'success',
                'data' => 'No payment Required'
            ]);

        $company = $em->getRepository(Company::class)->find(1);
    
        $tickets = [];

        if($request->request->get('adult') > 0)
            $tickets[] = $this->calculatePrice($translator->trans('adults'), $category->getDeposit(), $category->getAdultPrice()->getAmount(), $request->request->get('adult'));
        
        if($request->request->get('children') > 0)
            $this->calculatePrice($translator->trans('childrens'), $category->getDeposit(), $category->getChildrenPrice()->getAmount(), $request->request->get('children'));
        
        if($request->request->get('baby') > 0)
            $tickets[] = ['type' => $translator->trans('babies'), 'quantity' => $request->request->get('baby'), 'subtotal' => 0, 'total' => 0 ];

        return $this->render('taruga/category-payment.html',[
            'tickets' => $tickets,
            'category' => $category,
            'company' => $company,
            'payment_intent' => $stripe->createUpdatePaymentIntent($company, $request, null)
            ]);
    }


    private function calculatePrice($type, $deposit, $price, $quantity){

        $subTotal = $price * $quantity;
        
        $total = $deposit != 0 ? 
            (int) ($subTotal * (float)$deposit)
            :
            $subTotal;
        
        return ['type' => $type,'quantity' => $quantity, 'subtotal' => $subTotal, 'total' => $total];
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
        
        $em = $this->getDoctrine()->getManager();

        $totals = $em->getRepository(Category::class)->findAll();

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
                    
                    $category->setOrderBy(count($totals)+1);
                    $em->persist($category);
                    $em->flush();

                    ///$category->setOrderBy(count($totals));
                    //$em->persist($category);
                    //$em->flush();

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


    public function categoryList(Request $request, MoneyFormatter $moneyFormatter)
    {
        $em = $this->getDoctrine()->getManager();
     
        //$events = $em->getRepository(Event::class)->findAll();
        $categories = $em->getRepository(Category::class)->findBy([],['orderBy' => 'ASC']);

        $cA = array();

        foreach ($categories as $c){
            
            foreach ($c->getEvent() as $evt)
                $ev = $evt->getEvent();

            $cA[]=array(
                'id' => $c->getId(),
                'namePt' => $c->getNamePt(),
                'nameEn' => $c->getNameEn(),
                'descriptionPt' => $c->getDescriptionPt(),
                'descriptionEn' => $c->getDescriptionEn(),
                'adultAmount' =>$moneyFormatter->format($c->getAdultPrice()).'€',
                'childrenAmount' =>$moneyFormatter->format($c->getChildrenPrice()).'€',
                'warrantyPayment' => $c->getWarrantyPayment(),
                'warrantyPaymentPt' => $c->getWarrantyPaymentPt(),
                'warrantyPaymentEn' => $c->getWarrantyPaymentEn(),
                'highLight' => $c->getHighlight(),
                'isActive' => $c->getIsActive(),
                'availability' => $c->getAvailability(),
                'duration' => $c->getDuration(),
                'deposit' => $c->getDeposit()*100,
                'event' => $ev,
                'order' => $c->getOrderBy()
             );

        }    
        return $this->render('admin/category-list.html', array(
            'categories' =>  $cA));
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
        $em = $this->getDoctrine()->getManager();
        
        $category = $em->getRepository(Category::class)->find($categoryId);
        
        if (!$category) {
            return new JsonResponse(array('status'=> 0, 'message' => 'Categoria #'.$categoryId . ' não existe.'));
        }
        
        //search bookings if already bought this category, DO NOT DELETE send info to user
        $booking = $em->getRepository(Booking::class)->findDeleteCategory($category);
        
        if (count($booking) > 0)
            return new JsonResponse(array('status'=> 0, 'message' => $category->getNamePt() . ' não pode ser apagada. Já existem Reservas associadas'));

        else{

            $blocked = $em->getRepository(BlockDates::class)->findOneBy(['category' => $category]);
            $event = $em->getRepository(Event::class)->findOneBy(['category' => $category]);
            $available = $em->getRepository(Available::class)->findAll(['category' => $category]);
            
            if($available)

                foreach ( $available as $availables) {
                    $em->remove($availables);
                    $em->flush();
                }

            $img = $category->getImage();
            $em->remove($blocked);
            $em->remove($event);
            $em->remove($category);
            $em->flush();

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

            return new JsonResponse(array('status' => 1, 'message' => 'Categoria foi apagada'));
        }
        return new JsonResponse($response);
    }


    public function categoryOrder(Request $request)
    {
        $result = $request->request->get('result');

        if (!$result)
        
           return new JsonResponse(array('status'=> 0, 'message' => 'nada para ordenar', 'data' => null));

        $order = json_decode($result);
    
        $em = $this->getDoctrine()->getManager();  

        foreach ($order as $orderBy) {
            $category = $em->getRepository(Category::class)->find($orderBy->id);
            $category->setOrderBy($orderBy->to);
            $em->persist($category);
            $em->flush();
        }

        $response = array('status'=> 1, 'message' => 'success', 'data' => count($order));
        
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
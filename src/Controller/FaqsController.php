<?php
namespace App\Controller;

use App\Entity\Faqs;
use App\Entity\Locales;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\FaqsType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Translation\TranslatorInterface;

class FaqsController extends AbstractController
{

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function faqs(Request $request, ValidatorInterface $validator)

    {
        $em = $this->getDoctrine()->getManager();

        if($request->request->get('id')){
            $obj = $em->getRepository(Faqs::class)->find($request->request->get('id'));
            $form = $this->createForm(FaqsType::class, $obj);
        }
        else
        {
            $obj = $em->getRepository(Faqs::class)->findAll();
            $form = $this->createForm(FaqsType::class);

        } 
        
        $locales = $em->getRepository(Locales::class)->findAll();

        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){

                $obj->setLocales($locales);

                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();
                    $objs = $form->getData();

                    $objs->setLocales($locales);

                    $em->persist($objs);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $objs->getId());
                }
                else{   
                    $response = array(
                        'result' => 0,
                        'message' => 'fail',
                        'is_ok' =>$form["locales"]->getData(), 
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

        return $this->render('admin/faqs.html',array(
            'form' => $form->createView(),
            'obj' => $obj,
            'locales' => $locales
        ));

        return $this->render('admin/faqs.html');
    }


    public function faqsEdit(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        
        $obj = $em->getRepository(Faqs::class)->find($request->request->get('id'));

        $form = $this->createForm(FaqsType::class, $obj);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 

            $obj = $form->getData();

                try {
                    $em->persist($obj);
                    $em->flush();

                    $response = array(
                        'status' => 1,
                        'message' => 'Sucesso',
                        'data' => 'O registo '.$obj->getId().' foi gravado.');
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
                    'result' => 0,
                    'message' => 'fail',
                    'data' => $this->getErrorMessages($form)
                );
            }
        }
        return new JsonResponse($response);
    }


    public function faqsDelete(Request $request){

        $response = [];

        $obj_id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        
        $obj = $em->getRepository(Faqs::class)->find($obj_id);
       
        if (!$obj) {
            $response = ['status' => 0, 'message'=> 'Faqs #'.$obj_id . ' não existe.', 'data' =>'FAQS_NOT_FOUND'];
        }
        else{
            $em->remove($obj);
            $em->flush();

            $response = ['message'=>'success', 'status' => 1, 'data' => $obj_id];
        }
        return new JsonResponse($response);
    }



    public function faqsShow(Request $request, TranslatorInterface $translator)
    {

        $response = [];

        $em = $this->getDoctrine()->getManager(); 

        $local = $request->getLocale();

        $locale = $em->getRepository(Locales::class)->findOneby(['name' => $local]);

        $obj = $em->getRepository(Faqs::class)->findOneBy(['locales' => $locale]);
       
        $response = !$obj ?
            ['status' => 0, 'message' => $translator->trans('faqs_not_found'), 'data' => 'FAQS_NOT_FOUND']
            :
            ['status' => 1, 'message' => $obj->getName(), 'data' => $obj->getHtml()];

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

    private function getBrownserLocale($request) 
    { 
        $u_agent = $request->headers->get('accept-language');
        
        $locale = 'pt_PT';

        if (!preg_match('/pt-/i', $u_agent))
            $locale="en_EN";
        
        return $locale; 

    }


}

?>
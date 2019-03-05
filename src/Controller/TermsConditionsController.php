<?php
namespace App\Controller;

use App\Entity\TermsConditions;
use App\Entity\Locales;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\TermsConditionsType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;

class TermsConditionsController extends AbstractController
{

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function terms(Request $request, ValidatorInterface $validator)

    {
        $em = $this->getDoctrine()->getManager();

        if($request->request->get('id')){
            $terms = $em->getRepository(TermsConditions::class)->find($request->request->get('id'));
            $form = $this->createForm(TermsConditionsType::class, $terms);
        }
        else
        {
            $terms = $em->getRepository(TermsConditions::class)->findAll();
            $form = $this->createForm(TermsConditionsType::class);

        } 
        
        $locales = $em->getRepository(Locales::class)->findAll();

        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){

                $terms->setLocales($locales);

                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();
                    $terms = $form->getData();

                    $terms->setLocales($locales);

                    $em->persist($terms);
                    $em->flush();

                    $response = array(
                        'status' => 1,
                        'message' => 'success',
                        'data' => $terms->getId());
                }
                else{   
                    $response = array(
                        'status' => 0,
                        'message' => 'fail',
                        'is_ok' =>$form["locales"]->getData(), 
                        'data' => $this->getErrorMessages($form)
                    );
                }
            }
            else
                $response = array(
                    'status' => 2,
                    'message' => 'fail not submitted',
                    'data' => '');
                return new JsonResponse($response);
        }

        return $this->render('admin/terms-conditions.html',array(
            'form' => $form->createView(),
            'terms' => $terms,
            'locales' => $locales
        ));

        return $this->render('admin/terms-conditions.html');
    }


    public function termsEdit(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        
        $terms = $em->getRepository(TermsConditions::class)->find($request->request->get('id'));

        $form = $this->createForm(TermsConditionsType::class, $terms);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 

            $terms = $form->getData();

                try {
                    $em->persist($terms);
                    $em->flush();

                    $response = array(
                        'status' => 1,
                        'message' => 'Sucesso',
                        'data' => 'O registo '.$terms->getId().' foi gravado.');
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


    public function termsDelete(Request $request){

        $response = array();
        $termsId = $request->request->get('id');
        $entity = $this->getDoctrine()->getManager();
        
        $terms = $entity->getRepository(TermsConditions::class)->find($termsId);
       
        if (!$terms) {
            $response = array('message'=>'fail', 'status' => 'Termos & Condições #'.$termsId . ' não existe.');
        }
        else{
            $entity->remove($terms);
            $entity->flush();

            $response = array('message'=>'success', 'status' => $termsId);
        }
        return new JsonResponse($response);
    }



    public function termsShow(Request $request){

        $response = array();

        $em = $this->getDoctrine()->getManager(); 

        $locales = $em->getRepository(Locales::class)->findOneBy(['name' => $this->session->get('_locale')->getName()]);

        $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $locales]);
       
        $response = !$terms ?
            array('status' => 0, 'message' => 'Termos & Condições não encontrado', 'data' => null)
            :
            array('status' => 1, 'message' => $terms->getName(), 'data' => $terms->getTermsHtml());
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
<?php
namespace App\Controller;

use App\Entity\AboutUs;
use App\Entity\Locales;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\AboutUsType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;

class AboutUsController extends AbstractController
{

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }


    public function aboutUs(Request $request, ValidatorInterface $validator)

    {
        $em = $this->getDoctrine()->getManager();

        if($request->request->get('id')){
            $aboutUs = $em->getRepository(AboutUs::class)->find($request->request->get('id'));
            $form = $this->createForm(AboutUsType::class, $aboutUs);
        }
        else
        {
            $aboutUs = $em->getRepository(AboutUs::class)->findAll();
            $form = $this->createForm(AboutUsType::class);

        } 
        
        $locales = $em->getRepository(Locales::class)->findAll();

        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){

                $aboutUs->setLocales($locales);

                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();
                    $aboutUs = $form->getData();

                    $aboutUs->setLocales($locales);

                    $em->persist($aboutUs);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $aboutUs->getId());
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

        return $this->render('admin/about-us.html',array(
            'form' => $form->createView(),
            'aboutUs' => $aboutUs,
            'locales' => $locales
        ));

        return $this->render('admin/about-us.html');
    }



    public function aboutUsEdit(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        
        $aboutUs = $em->getRepository(AboutUs::class)->find($request->request->get('id'));

        $form = $this->createForm(AboutUsType::class, $aboutUs);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 

            $aboutUs = $form->getData();

                try {
                    $em->persist($aboutUs);
                    $em->flush();

                    $response = array(
                        'status' => 1,
                        'message' => 'Sucesso',
                        'data' => 'O registo '.$aboutUs->getId().' foi gravado.');
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


    public function aboutUsDelete(Request $request){

        $response = array();
        $aboutUsId = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        
        $aboutUs = $em->getRepository(AboutUs::class)->find($aboutUsId);
       
        if (!$aboutUs) {
            $response = array('message'=>'fail', 'status' => 'Registo #'.$aboutUsId . ' não existe.');
        }
        else{
            $em->remove($aboutUs);
            $em->flush();

            $response = array('message'=>'success', 'status' => $aboutUsId);
        }
        return new JsonResponse($response);
    }


    public function aboutUsShow(Request $request){

        $response = array();

        $em = $this->getDoctrine()->getManager(); 

        $locales = $em->getRepository(Locales::class)->findOneBy(['name' => $this->session->get('_locale')->getName()]);

        $aboutUs = $em->getRepository(AboutUs::class)->findOneBy(['locales' => $locales]);
       
        $response = !$aboutUs ?
            array('status' => 0, 'message' => 'Registo não encontrado', 'data' => null)
            :
            array('status' => 1, 'message' => $aboutUs->getName(), 'data' => $aboutUs->getRgpdHtml());
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
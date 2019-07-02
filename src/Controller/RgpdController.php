<?php
namespace App\Controller;

use App\Entity\Rgpd;
use App\Entity\Locales;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\RgpdType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Translation\TranslatorInterface;

class RgpdController extends AbstractController
{

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function rgpd(Request $request, ValidatorInterface $validator)

    {
        $em = $this->getDoctrine()->getManager();

        if($request->request->get('id')){
            $rgpd = $em->getRepository(Rgpd::class)->find($request->request->get('id'));
            $form = $this->createForm(RgpdType::class, $rgpd);
        }
        else
        {
            $rgpd = $em->getRepository(Rgpd::class)->findAll();
            $form = $this->createForm(RgpdType::class);

        } 
        
        $locales = $em->getRepository(Locales::class)->findAll();

        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){

                $rgpd->setLocales($locales);

                if($form->isValid()){

                    $em = $this->getDoctrine()->getManager();
                    $rgpds = $form->getData();

                    $rgpds->setLocales($locales);

                    $em->persist($rgpds);
                    $em->flush();

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $rgpds->getId());
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

        return $this->render('admin/rgpd.html',array(
            'form' => $form->createView(),
            'rgpd' => $rgpd,
            'locales' => $locales
        ));

        return $this->render('admin/rgpd.html');
    }


    public function rgpdEdit(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        
        $rgpd = $em->getRepository(Rgpd::class)->find($request->request->get('id'));

        $form = $this->createForm(RgpdType::class, $rgpd);

        $form->handleRequest($request);
            
        if($form->isSubmitted()){
                
            if($form->isValid()){ 

            $rgpd = $form->getData();

                try {
                    $em->persist($rgpd);
                    $em->flush();

                    $response = array(
                        'status' => 1,
                        'message' => 'Sucesso',
                        'data' => 'O registo '.$rgpd->getId().' foi gravado.');
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


    public function rgpdDelete(Request $request){

        $response = array();
        $rgpdId = $request->request->get('id');
        $entity = $this->getDoctrine()->getManager();
        
        $rgpd = $entity->getRepository(Rgpd::class)->find($rgpdId);
       
        if (!$rgpd) {
            $response = array('message'=>'fail', 'status' => 'Rgpd #'.$rgpdId . ' não existe.');
        }
        else{
            $entity->remove($rgpd);
            $entity->flush();

            $response = array('message'=>'success', 'status' => $rgpdId);
        }
        return new JsonResponse($response);
    }



    public function rgpdShow(Request $request, TranslatorInterface $translator)
    {

        $response = array();

        $em = $this->getDoctrine()->getManager(); 

        $local = $request->getLocale();

        $locale = $em->getRepository(Locales::class)->findOneby(['name' => $local]);

        $rgpd = $em->getRepository(Rgpd::class)->findOneBy(['locales' => $locale]);
       
        $response = !$rgpd ?
            array('status' => 0, 'message' => $translator->trans('gpdr_not_found'), 'data' => 'GPDR_NOT_FOUND')
            :
            array('status' => 1, 'message' => $rgpd->getName(), 'data' => $rgpd->getRgpdHtml());

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
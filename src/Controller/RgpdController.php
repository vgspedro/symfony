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
        
        if ($request->isXmlHttpRequest() && $request->request->get($form->getName())) {

            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted()){

                $locales = $em->getRepository(Locales::class)->find(1);

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
            'rgpd' => $rgpd
        ));

        return $this->render('admin/rgpd.html');
    }



    public function rgpdEdit(Request $request, ValidatorInterface $validator)
    {
        $rgpd = $em->getRepository(Rgpd::class)->find($request->request->get('id'));
        $form = $this->createForm(RgpdType::class, $rgpd);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


        }
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


    public function rgpdShow(Request $request){

        $response = array();

        $em = $this->getDoctrine()->getManager(); 

        $locales = $em->getRepository(Locales::class)->findOneBy(['name' => $this->session->get('_locale')->getName()]);

        $rgpd = $em->getRepository(Rgpd::class)->findOneBy(['locales' => $locales]);
       
        $response = !$rgpd ?
            array('status' => 0, 'message' => 'Rgpd não encontrado', 'data' => null)
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

}

?>
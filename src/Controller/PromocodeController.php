<?php
namespace App\Controller;

use App\Entity\Promocode;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\PromocodeType;
use Doctrine\DBAL\DBALException;

class PromocodeController extends AbstractController
{

    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $promocodes = $em->getRepository(Promocode::class)->findAll();
        $form = $this->createForm(PromocodeType::class);

        return $this->render('admin/promocode-list.html', [
            'form' => $form->createView(),
            'promocodes' => $promocodes
        ]);
    }

    public function submit(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $err = [];
        $id = $request->request->get('id');

        $promocode = $id ? $em->getRepository(Promocode::class)->find($id) : null;
        
        if(!$promocode)
            $promocode = new Promocode();

        $form = $this->createForm(PromocodeType::class, $promocode);
        $form->handleRequest($request);

        $form->get('start')->getData() ? false : $err [] = 'Data Inicio Obrigatório';
        $form->get('end')->getData() ? false : $err [] = 'Data Fim Obrigatório';
        $form->get('code')->getData() ? false : $err [] = 'Código Obrigatório';
        $form->get('discount')->getData() ? false : $err [] = 'Desconto Obrigatório';

        //$form->get('isActive')->getData();
        if (count($err) > 0 )
            return new JsonResponse([
                'status' => 2,
                'message' => 'fields_fail',
                'data' =>  $err
            ]);
        
        $form->get('discount')->getData() < 1 || $form->get('discount')->getData() > 100 ? $err [] = 'Desconto, entre 1 e 100 apenas' :
        false;

        if (count($err) > 0 )
            return new JsonResponse([
                'status' => 2,
                'message' => 'fields_fail',
                'data' =>  $err
            ]);

        $start = \DateTime::createFromFormat('d/m/Y',  $form->get('start')->getData(), new \DateTimeZone('Europe/Lisbon'));
        $end = \DateTime::createFromFormat('d/m/Y',  $form->get('end')->getData(), new \DateTimeZone('Europe/Lisbon'));

        if($form->isSubmitted() && $form->isValid()){

            $promocode = $form->getData();
            
            $em->getConnection()->beginTransaction();
            
            try {
                $promocode->setStart($start);
                $promocode->setEnd($end);

                $em->persist($promocode);
                $em->flush();
                $em->getConnection()->commit();
                
                return new JsonResponse([
                    'status' => 1,
                    'message' => 'success',
                    'data' => 'Promocode #'.$promocode->getId().', sucesso.'
                ]);
            } 
            catch(DBALException $e){
                $em->getConnection()->rollBack();
                return new JsonResponse([
                    'status' => 0,
                    'message' => 'db_fail',
                    'data' => $e->getMessage()
                ]);
            }
        }
        return new JsonResponse([
            'result' => 0,
            'message' => 'fail',
            'data' => $this->getErrorMessages($form)]);
    }



    public function delete(Request $request)
    {
        $id = $request->request->get('id');
       
        if (!$id)
            return new JsonResponse([
                'status' => 0,
                'message' => 'Promocode não encontrado (#'.$id.'-POST)!',
                'data' => $id
            ]);

        $em = $this->getDoctrine()->getManager();
        $promocode = $em->getRepository(Promocode::class)->find($id);
        
        if (!$promocode)
            return new JsonResponse([
                'status' => 0,
                'message' =>  'Promocode não encontrado (#'.$id.'-ENTITY)!',
                'data' => $id
            ]);
        
        try{
            $em->remove($promocode);
            $em->flush();
        }
        catch(DBALException $e){
                $em->getConnection()->rollBack();
                return new JsonResponse([
                    'status' => 2,
                    'message' => 'db_fail',
                    'data' => $e->getMessage()
                ]);
        }
        return new JsonResponse([
            'status'=> 1,
            'data' => $id,
            'message' => 'Promocode #'.$id.' foi apagado.'
        ]);
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
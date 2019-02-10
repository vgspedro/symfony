<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Available;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailableController extends AbstractController
{

    public function adminAvailable(Request $request)
    {
        return $this->render('admin/available.html');
    }

    public function adminAvailableNew(Request $request, ValidatorInterface $validator)
    {

        $allEvents = array();

        $categoryId = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($categoryId);

        $event = explode(',',$category->getEvent()[0]->getEvent());
        
        foreach ($event as $ev)
            array_push($allEvents, $ev);

        return $this->render('admin/available-new.html',array(
            'category' => $category,
            'event' => $allEvents
        ));
    }


    public function adminAvailableCreate(Request $request){

        $em = $this->getDoctrine()->getManager();
        
        $category = $em->getRepository(Category::class)->find($request->request->get('category'));

        $available = new Available();

        $date = \DateTime::createFromFormat('d/m/Y H:i', $request->request->get('startDate').' '.$request->request->get('event'));

        $available->setCategory($category);
        $available->setLotation($category->getAvailability());
        $available->setStock($category->getAvailability());
        $available->setDatetime($date);

        $em->persist($available);
        $em->flush();  

        $response = array(
            'result' => 1,
            'message' => 'ok',
            'data' => $request->request->get('category'));

        return new JsonResponse($response);

   }

}
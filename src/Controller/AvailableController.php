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

        $eventStart = \DateTime::createFromFormat('d/m/Y H:i', $request->request->get('startDate').' '.$request->request->get('event'));
        $s = explode(":",$category->getDuration());

        $seconds = (int)$s[0]*3600 + (int)$s[1]*60;

        $eventEnd = \DateTime::createFromFormat('U', ($eventStart->format('U') + $seconds));

        $available = new Available();

        $available->setCategory($category);
        $available->setLotation($category->getAvailability());
        $available->setStock($category->getAvailability());
        $available->setDatetimeStart($eventStart);
        $available->setDatetimeEnd($eventEnd);

        $em->persist($available);
        $em->flush();  

        $response = array(
            'result' => 1,
            'message' => 'ok',
            'data' => $request->request->get('category'));

        return new JsonResponse($response);

   }


    /**
     * Gets events for the calendar (only accessible by ajax).
     * @param int $productId
     * @param Request $request     
     */
    public function adminListAvailable(Request $request)
    {       
        $em = $this->getDoctrine()->getManager();
        
        $categoryId = $request->request->get('category') ? $request->request->get('category') : null;
        $startDate = $request->request->get('start');
        $endDate = $request->request->get('end');
        
        if($categoryId)
            $category = $em->getRepository(Category::class)->find($categoryId);

        if(!$categoryId)
            $availables = $em->getRepository(Available::class)->findAll();
        
        $data_events = array();
        
        $data_resources = array();

        $rand_color = array('','blue','green','black','blueviolet','brown','cadetblue','cornflowerblue','darkcyan','orange');

        $id = null;

        $counter = 0;

        foreach ($availables as $available) {
            if(!$id || $id != $available->getCategory()->getId()){
                $counter++;    
                $id = $available->getCategory()->getId(); 
                $data_resources[] = array(
                'id' => $available->getCategory()->getId(),
                'title' => $available->getCategory()->getNamePt(),
                'eventColor' => $rand_color[$counter]
                );
            }

            $finalEnd = str_replace(' ','T',$available->getDatetimeEnd()->format('Y-m-d H:i:s'));
            $finalStart = str_replace(' ','T',$available->getDatetimeStart()->format('Y-m-d H:i:s'));

            $data_events[] = array(
                'id' => $available->getId(),
                'resourceId' => $available->getCategory()->getId(),          
                'start' => $finalStart,
                'end' => $finalEnd,
                'title' =>'Total: '.$available->getLotation().' Disponivel: '.$available->getStock(),
                'textColor' => $available->getStock().'**'.$available->getLotation().'**'.$available->getCategory()->getNamePt().'<br>Data: '.$available->getDatetimeStart()->format('d/m/Y H:i'),
            );
        }
        
        return $this->json(array('events' => $data_events, 'resources' => $data_resources));        
    }

    public function adminAvailableEdit(Request $request){

        $em = $this->getDoctrine()->getManager();
        
        $available = $em->getRepository(Available::class)->find($request->request->get('id'));

        $available->setLotation($request->request->get('lotation'));
        $available->setStock($request->request->get('stock'));

        $em->persist($available);
        $em->flush();  

        $response = array(
            'status' => 1,
            'message' => 'ok',
            'data' => $request->request->get('category'));

        
        return new JsonResponse($response);
   }

    public function adminAvailableDelete(Request $request){

        $em = $this->getDoctrine()->getManager();
        
        $available = $em->getRepository(Available::class)->find($request->request->get('id'));

        if ($available->getLotation() == $available->getStock()){
            $entityManager->remove($product);
            $entityManager->flush();

            $response = array(
                'result' => 1,
                'message' => 'deleted',
                'data' => $request->request->get('id'));
            }
        else 
            $response = array(
                'result' => 0,
                'message' => 'not possible',
                'data' => $request->request->get('id'));

        return new JsonResponse($response);

   }

}
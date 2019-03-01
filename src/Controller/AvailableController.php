<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Available;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DBALException;

class AvailableController extends AbstractController
{

    public function adminAvailable(Request $request)
    {
        return $this->render('admin/available.html');
    }

    //TEMPLATE LOAD
    public function adminAvailableNew(Request $request)
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
        
        if(!$category) {

            $response = array(
                'status' => 0,
                'message' => 'Categoria não encontrada!',
                'data' => null);

            return new JsonResponse($response);
        }

        $eventStart = \DateTime::createFromFormat('d/m/Y H:i', $request->request->get('startDate').' '.$request->request->get('event'));
        
        $s = explode(":",$category->getDuration());

        $seconds = (int)$s[0]*3600 + (int)$s[1]*60;
        //duration of event
        $interval = \DateTime::createFromFormat('U', ($eventStart->format('U') + $seconds));
        
        if($request->request->get('endDate'))
            $eventEnd = \DateTime::createFromFormat('d/m/Y', $request->request->get('endDate'));
        
        $isRecurrent = $request->request->get('startDate') != $request->request->get('endDate') && $request->request->get('endDate') ? 
        true : false;

        $eventsCreated = 0;

        // User wants to create a recurrent event.
        if ($isRecurrent) {

            $rule = (new \Recurr\Rule)
                ->setStartDate($eventStart)
                ->setTimezone('Europe/Lisbon')
                ->setFreq('DAILY')
                ->setUntil($eventEnd);

            $transformer = new \Recurr\Transformer\ArrayTransformer();

            $recurrencyRef = 0;

            foreach ($transformer->transform($rule) as $day) {

             //We do not allow to create an event for this product with the same date and start time.

                $starts = \DateTime::createFromFormat('d/m/Y H:i', $day->getStart()->format('d/m/Y').' '.$request->request->get('event'));

                $eventWithSameDateAndHour = $em->getRepository(Available::class)->count(array(
                'category' => $category,
                'datetimestart' => $starts
                ));
                
                if ($eventWithSameDateAndHour) {
                    $response = array(
                        'status' => 0,
                        'message' => 'No periodo temporal inserido, já existe pelo menos 1 disponbilidade criada, escolha outra hora!',
                        'data' => null);
                        // We must to delete the first event because it was already saved in DB.
                    if ($recurrencyRef > 0) {
                    // First detach all managed entities; otherwise they will be saved.
                    $em->clear();
                    // Merge the firstEvent so we can remove it from DB.
                    $firstEvent = $em->merge($firstEvent);
                    $em->remove($firstEvent);
                    $em->flush();
                }

                $response = array(
                    'status' => 0,
                    'message' => 'No periodo temporal inserido, já existe pelo menos 1 disponibilidade criada, escolha outra hora!',
                    'data' => null);
                return new JsonResponse($response);
            
            }

            $eventsCreated++;

            $dayEventEnd = \DateTime::createFromFormat('d/m/Y H:i', $day->getStart()->format('d/m/Y').' '.$interval->format('H:i'));
            $available = new Available();
            $available->setDatetimeStart($day->getStart());
            $available->setCategory($category);
            $available->setLotation($category->getAvailability());
            $available->setStock($category->getAvailability());
            $available->setDatetimeEnd($dayEventEnd);
            $em->persist($available);
        }

        $em->flush();

        }
        else{

            $eventsCreated++;

            $dayEventEnd = \DateTime::createFromFormat('d/m/Y H:i', $request->request->get('startDate').' '.$interval->format('H:i'));
            $available = new Available();
            $available->setDatetimeStart($eventStart);
            $available->setCategory($category);
            $available->setLotation($category->getAvailability());
            $available->setStock($category->getAvailability());
            $available->setDatetimeEnd($dayEventEnd);
            $em->persist($available);
            $em->flush();
        }

        $response = array(
            'status' => 1,
            'message' => 'Foram criadas '.$eventsCreated.' disponibilidades, na Categoria '.$category->getNamePt(),
            'recurrent' => $isRecurrent,
            'data' => null,
            );
        return new JsonResponse($response);
   }


    /**
     * Gets events for the calendar (only accessible by ajax).
     * @param int $productId
     * @param Request $request     
     */
    public function adminAvailableList(Request $request)
    {       
        $em = $this->getDoctrine()->getManager();
        
        $start = \DateTime::createFromFormat('U', $request->query->get('start'));

        $end = \DateTime::createFromFormat('U', $request->query->get('end'));

        $categories = $em->getRepository(Category::class)->findAll();

        $availables = $em->getRepository(Available::class)->findAvailableFromInterval($start, $end);

        $data_events = array();
        
        $data_resources = array();

        $rand_color = array('','blue','green','black','blueviolet','brown','cadetblue','cornflowerblue','darkcyan','orange');

        $id = null;

        $counter = 0;

        foreach ($categories as $category) {
            $counter++;
            $data_resources[] = array(
                'id' => $category->getId(),
                'title' => $category->getNamePt(),
                'eventColor' => $rand_color[$counter]
            );
        }

        foreach ($availables as $available) {

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

        if(!$available)
            $response = array(
                'status' => 0,
                'message' => 'Disponibilidade não encontrada!',
                'data' => null);

        $available->setLotation($request->request->get('lotation'));
        $available->setStock($request->request->get('stock'));

        try {
            $em->persist($available);
            $em->flush();  

            $response = array(
                'status' => 1,
                'message' => 'success',
                'data' => ['id' => $available->getId(), 'stock' => $available->getStock(), 'lotation' => $available->getLotation() ]
            );

        } catch (Exception $e) {
       
            $response = array(
                'status' => 0,
                'message' => 'Exception error',
                'data' => $e->getMessage().' '.$available->getId()
                );
        }

        return new JsonResponse($response);
   }

    public function adminAvailableDelete(Request $request){

        $em = $this->getDoctrine()->getManager();
        
        $available = $em->getRepository(Available::class)->find($request->request->get('id'));

        if ($available->getLotation() == $available->getStock()){
            $entityManager->remove($available);
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
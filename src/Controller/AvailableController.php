<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Booking;
use App\Entity\Event;
use App\Entity\Logs;
use App\Entity\Available;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Translation\TranslatorInterface;


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

    public function adminAvailableResourcesActions(Request $request){

        $categoryId = $request->request->get('resource-id') ? $request->request->get('resource-id') : '';
        $start = $request->request->get('start-date') ? $request->request->get('start-date') : '' ;
        $end = $request->request->get('end-date') ? $request->request->get('end-date') : '';
        $stock = $request->request->get('stock') ? $request->request->get('stock') : '';
        
        //action 0 delete || 1 edit
        $action = $request->request->get('action');

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($categoryId);

        if(!$category)    
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Categoria não foi encontrada!',
                'data' => null));
        
        if(!$start && !$end && $action == 1 && !$stock ) 
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Insira um periodo Inicio, Fim e o Stock!',
                'data' => null));

        else if(!$start && !$end && $action == 0 ) 
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Insira um periodo Inicio e Fim!',
                'data' => null));

        else if(!$start || !$end) 
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Insira um periodo Inicio e Fim!',
                'data' => null));

        $start = \DateTime::createFromFormat('d/m/Y', $start);
        $start->setTime(00, 00, 00);
        $end = \DateTime::createFromFormat('d/m/Y', $end);
        $end->setTime(23, 59, 59);

        //edit stock of category

        if ($action == 1){
    
            $availables = $em->getRepository(Available::class)->findAvailableFromIntervalAndCategory($start, $end, $category);
            
            foreach ($availables as $available){
                if($stock > $category->getAvailability())
                    $stock = $category->getAvailability();
                $available->setStock($stock);
                $em->persist($available);
            }
            
            $em->flush();

            $response = array(
                'status' => 1,
                'message' => 'Editadas '.count($availables).' disponibilidades.<br>Categoria: '.$category->getNamePt().'<br>Datas: '.$start->format('d/m/Y').' @ '.$end->format('d/m/Y'),
                'data' => null,
            );
        }
        
        //DELETE
        else {

            $undeletable = array();

            //CHECK IF IN DATE PERIOD ON WE HAVE BOOKINGS 
            $hasBookings = $em->getRepository(Available::class)->findAvailableByDatesAndCategoryBookingJoin($start, $end, $category);

            if($hasBookings){

                foreach ($hasBookings as $av)
                    $undeletable[] = $av['id'];
                
                //GET ALL AVAILABLES EXCEPT THE undeletable, THE ONES WITH BOOKINGS
                $findNoBookings = $em->getRepository(Available::class)->findAvailableWithDatesAndCategoryNoBookingJoin($start, $end, $undeletable, $category);
                    
                foreach ($findNoBookings as $deletable){
                    $available = $em->getRepository(Available::class)->find($deletable);
                    $em->remove($available);
                }
                
                $em->flush();
                
                $message = count($undeletable) > 0 ? 
                    'Apagadas '.count($findNoBookings).', ficaram por apagar '.count($undeletable).' disponibilidades, por ter reservas associadas.<br>Categoria '.$category->getNamePt().'<br>Datas '.$start->format('d/m/Y').' @ '.$end->format('d/m/Y')
                    :
                    'Apagadas '.count($findNoBookings).' disponibilidades.<br>Categoria: '.$category->getNamePt().'<br>Datas: '.$start->format('d/m/Y').' @ '.$end->format('d/m/Y');

                $response = array(
                    'status' => 1,
                    'message' => $message,
                    'data' => null,
                );
            }
            else{

                $availables = $em->getRepository(Available::class)->findAvailableFromIntervalAndCategory($start, $end, $category);
                
                foreach ($availables as $available)
                    $em->remove($available);
                $em->flush();

                $response = array(
                    'status' => 1,
                    'message' => 'Apagadas '.count($availables).' disponibilidades.<br>Categoria: '.$category->getNamePt().'<br>Datas: '.$start->format('d/m/Y').' @ '.$end->format('d/m/Y'),
                    'data' => null, 
                );
            }
        }
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

        $categories = $em->getRepository(Category::class)->findBy([],['orderBy' => 'ASC']);

        $availables = $em->getRepository(Available::class)->findAvailableFromInterval($start, $end);

        $data_events = array();
        
        $data_resources = array();

        $rand_color = array('','blue','green','black','blueviolet','brown','forestgreen','cadetblue','cornflowerblue','chocolate','darkcyan','orange','darkgoldenrod');

        $id = null;

        $counter = 0;

        foreach ($categories as $category) {
            $counter++;
            $data_resources[] = array(
                'eventColor' => $rand_color[$counter],
                'id' => $category->getId(),
                'lotation' => $category->getAvailability(),
                'title' => $category->getNamePt(),
                'order' => $category->getOrderBy()
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
                'title' =>'Total: '.$available->getLotation().' Stock: '.$available->getStock(),
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
        
        if(!$available)
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Disponibilidade não foi encontrada!',
                'data' => null));

        $booking = $em->getRepository(Booking::class)->findOneBy(['available' => $available]);
            
        if($booking)
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Não é possivel apagar a Disponibilidade, tem reservas associadas!',
                'data' => null));
        
        $logTxt = 'Utilizador: '.$this->getUser()->getUsername().'Evento: #'.$available->getId().'
        Start: '.$available->getDatetimeStart()->format('d/m/Y H:i:s').' End: '.$available->getDatetimeEnd()->format('d/m/Y H:i:s').'
        Lotação : '.$available->getLotation().' Stock : '.$available->getStock().'Categoria: '.$available->getCategory()->getNamePt();

        $now = new \DateTime('now');
        $log = new Logs();
        $log->setDatetime($now);
        $log->setLog($logTxt);
        $log->setStatus('delete');
        $em->persist($log);
        $em->flush();

        $em->remove($available);
        $em->flush();

        return new JsonResponse(array(
            'status' => 1,
            'message' => 'Disponibilidade foi Apagada',
            'data' => $request->request->get('id')));
    }

/**
*Get the availability of a category starting tomorrow in a period of 8 days
*
**/
    public function getCategoryPeriodAvailability(Request $request, TranslatorInterface $translator) {
    
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($request->request->get('category'));

        if(!$category)
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Categoria não foi encontrada!',
                'data' => null));

        $total_pax = $request->request->get('adult') > 0 ? $request->request->get('adult') : 0;

        if($total_pax == 0)
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Pedido tem q ser mais q 0(zero )!',
                'data' => null));

        $total_pax = (int)$request->request->get('adult') + (int)$request->request->get('children') + (int)$request->request->get('baby');

        $next = $request->request->get('next') ? $request->request->get('next') : 1;

        //if 0 is the previous week
        if($next == 0){
            $start = $request->request->get('start') ? \DateTime::createFromFormat('d/m/Y', $request->request->get('start')) : new \DateTime('tomorrow');
            $end = $start;
            $end->add(new \DateInterval('P8D'));
        } 
        else{
            $start = $request->request->get('start') ? \DateTime::createFromFormat('d/m/Y', $request->request->get('start')) : new \DateTime('tomorrow');
            $end = $start;
            $end->add(new \DateInterval('P8D'));
        }

        $availability = $em->getRepository(Available::class)->findCategoryAvailabilityByWeekAndPax($category, $start, $end, $next, $total_pax);

        $d = [];
        $h = [];

        foreach ($availability as $a)
            $d[] = [
                'date' => $a->getDatetimestart()->format('Y-m-d'),
                'day' => $translator->trans(strtolower ($a->getDatetimeStart()->format('D'))).' '.$a->getDatetimeStart()->format('d'),
                'week_day_year' => (int) $a->getDatetimeStart()->format('d'),
                'week_day' => (int) $a->getDatetimeStart()->format('w'),
                'id' => $a->getId(),
                'hour' => $a->getDatetimeStart()->format('H:i'),
            ];
        
/*
        foreach ($d as $day) {
        
        }
*/

        return new JsonResponse([
            'status' => 1,
            'message' => 'Disponibilidade foi Apagada',
            'data' => $d

            ]);
    }

}
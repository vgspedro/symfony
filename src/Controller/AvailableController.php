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
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class AvailableController extends AbstractController
{

    private $session;

    private $in_advance_hours;

    public function __construct(SessionInterface $session)
    {
        $this->in_advance_hours = '13:03:00';
        $this->session = $session; 
    }


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

        $data_events = [];
        
        $data_resources = [];

        $rand_color = ['blue','green','black','blueviolet','brown','cadetblue','coral','indigo','olivedrab','orange','darkgoldenrod'];

        $id = null;

        $counter = 0;

        foreach ($categories as $category) {
            $data_resources[] = [
                'eventColor' => $rand_color[$counter],
                'id' => $category->getId(),
                'lotation' => $category->getAvailability(),
                'title' => $category->getNamePt(),
                'order' => $category->getOrderBy()
            ];
            $counter++;
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

        $err=[];
        
        $request->request->get('category') ? '' : $err[] = $translator->trans('part_seven.tour');
        !$request->request->get('adult') || $request->request->get('adult') <= 0 ? $err[] = $translator->trans('part_seven.adult') : $adult = $request->request->get('adult');
        $children = !$request->request->get('children') || $request->request->get('children') <= 0 ? 0 : $request->request->get('children');
        $baby = !$request->request->get('baby') || $request->request->get('baby') <= 0 ? 0 : $request->request->get('baby');

        if ($err)
            return new JsonResponse([
                'status' => 2,
                'message' => 'Fields missing!',
                'data' => $err
            ]);

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($request->request->get('category'));

        if(!$category)
            return new JsonResponse([
                'status' => 2,
                'message' => $translator->trans('part_seven.no_stock'),
                'data' => null
            ]);

        $total_pax = $adult + $children + $baby;

        $next = $request->request->get('next') ? $request->request->get('next') : 1;
        
        $now = new \DateTime('now', new \DateTimeZone('Europe/Lisbon'));
        $tomorrow = new \DateTime('tomorrow', new \DateTimeZone('Europe/Lisbon'));
        //if -1 is the previous week
        if($next == -1){

            if($request->request->get('date'))
                $start = \DateTime::createFromFormat('Y-m-d', $request->request->get('date'), new \DateTimeZone('Europe/Lisbon'));
            else{
                if($now->format('Y-m-d H:i:s') >= $now->format('Y-m-d '.$this->in_advance_hours) && $now->format('Y-m-d H:i:s') < $tomorrow->format('Y-m-d 00:00:00')) 
                    $start = new \DateTime('tomorrow +1 day', new \DateTimeZone('Europe/Lisbon'));
                else 
                    $start = new \DateTime('tomorrow', new \DateTimeZone('Europe/Lisbon'));
            }
        } 
        else{
            if($request->request->get('date'))
                $start = \DateTime::createFromFormat('Y-m-d', $request->request->get('date'), new \DateTimeZone('Europe/Lisbon'));
            else{
                if($now->format('Y-m-d H:i:s') >= $now->format('Y-m-d '.$this->in_advance_hours) && $now->format('Y-m-d H:i:s') < $tomorrow->format('Y-m-d 00:00:00')) 
                    $start = new \DateTime('tomorrow +1 day', new \DateTimeZone('Europe/Lisbon'));
                else 
                    $start = new \DateTime('tomorrow', new \DateTimeZone('Europe/Lisbon'));
            }
        }

        $availability = $em->getRepository(Available::class)->findCategoryAvailabilityByWeekAndPax($category, $start, $total_pax);

        //To set the available days in datepicker
        $ad = [];
        //To show only one week at a time
        $unique =[];

        foreach ($availability as $a){
            array_push($ad, $a->getDatetimestart()->format('Y-m-d'));
            $r = array_unique($ad);
            
            if(!in_array($a->getDatetimeStart()->format('Y-m-d'), $unique, true))
                if (count($unique) < 7)
                    array_push($unique, $a->getDatetimestart()->format('Y-m-d'));    
            
            if (count($r) < 8 )
                $d[] = [ 
                    'date_ymd' => $a->getDatetimestart()->format('Y-m-d'),
                    'date_dmy' => $a->getDatetimestart()->format('d/m/Y'),
                    'id' => $a->getId(),
                    'hour' => $a->getDatetimeStart()->format('H:i')          
                ];            
        }

        $temp = [];
        $final = [];

        foreach ($unique as $d_unique) {

            foreach ($d as $avlb) 
                if ($d_unique == $avlb['date_ymd'])
                    $temp[] = $avlb;

            $day_week = \DateTime::createFromFormat('Y-m-d', $d_unique);
 
             $final[] = [
                'date_ymd' => $day_week->format('Y-m-d'),
                'date_dmy' => $day_week->format('d/m/Y'),
                'day_week' => $translator->trans(strtolower ($day_week->format('D'))).' '.$day_week->format('d'),
                'event' => $temp
            ];
            
            $temp = []; 
        
        }
        
        //since we have stock, set the expiration time to end the booking process
        $this->session->set('start_time', $request->server->get('REQUEST_TIME'));

        return new JsonResponse([
            'status' => 1,
            'message' => 'success',
            'data' => [ 
                'available_dates' => $ad,//Build calendar
                'availability' => $category->getAvailability(),
                'payment' => $category->getWarrantyPayment(),
                'week' => $final,
                'expiration' => 900,
                'expiration_start' => $this->session->get('start_time'),
                'now' => $now->format('Y-m-d H:i:s'),
                'start' => $start->format('Y-m-d H:i:s') 
                ] //Build the week
            ]);
    }

}
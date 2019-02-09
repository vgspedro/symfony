<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Blockdates;
use App\Entity\Event;
use App\Entity\Category;
use App\Entity\Available;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Locales;

class BookingController extends AbstractController
{

    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function getAvailable(Request $request)
    {

        $err = array();
        $totalPax = 0;
        $categoryId = $request->request->get('category') ? $request->request->get('category') : $err[] = 'TOUR';
        $adult = $request->request->get('adult') || $request->request->get('adult') === "0" ? $request->request->get('adult') : $err[] = 'ADULT';
        $children = $request->request->get('children') || $request->request->get('children') === "0" ? $request->request->get('children') : $err[] = 'CHILDREN';
        $baby = $request->request->get('baby') || $request->request->get('baby') === "0" ? $request->request->get('b') :  $err[] = 'BABY';
        $totalPax = (int) $baby + (int)$children + (int)$adult == 0 ? $err[] = 'ZERO' : (int) $baby + (int)$children + (int)$adult ;

        if ($err) {
            $response = array(
                'status' => 0,
                'message' => $err,
                'event' => null,
                'blocked'=> null,
                'blocktype'=> null,
                'minDate' => null,
                'locale' => $this->session->get('_locale')->getName()
            );
            return new JsonResponse($response);
        }





        /*check if available exits*/
        $em = $this->getDoctrine()->getManager();
        
        $category = $available = $em->getRepository(Category::class)->find($categoryId);

        $allHours = array(); 

        $startdt = new \DateTime('tomorrow');
       
        foreach ($category->getEvent() as $time) {
            
            /*all hour defined for the category*/
            $schedule = explode($time->getEvent(),',');
            
            /*hours defined */ 
            $event = explode($schedule[0] ,':');

            var_dump($schedule);

            $startdt->setTime($event[0], $event[1]);
            $startdt->format('Y-m-d H:i:s');
            $allHours[] = $startdt;


        }

        $available = $em->getRepository(Available::class)->findByCategoryDateTomorrow($category, $allHours, $totalPax);

        $blockingDate = array();
        
        $now = new \Datetime('tomorrow');
        
        $minDate='';

        $blockedDates = $this->getDoctrine()->getRepository(Blockdates::class)
        ->findOneBy(['category' => $categoryId]);

        $blockedDates->getOnlyDates() == 0 ? $blockingDate[] = $now->format('Y-m-d') : $blockingDate = array();

        if ($blockedDates){

            $minDate = $now->format('Y-m-d');
            
            if($blockedDates->getDate()){
                $dates = explode(',',$blockedDates->getDate());

                for($r = 0; $r < count($dates); $r++){
                    $date = date_create_from_format("d/m/Y",$dates[$r]);
                    $blockingDate[] = $date->format('Y-m-d');
                }
            }
        }

        if ($blockedDates->getOnlyDates() == 1 && !$blockedDates->getDate()){
            $minDate='';
            $blockingDate = array();
        }
        $event = $this->getDoctrine()->getRepository(Event::class)
        ->findOneBy(['category' => $categoryId]);

        !$event ? 
            $response = array(
                'status' => 0,
                'message' => 'event not found',
                'data' => 'fail',
                'event' => null,
                'blocked' => null,
                'blocktype' => null,
                'minDate' => null,
                'available' => $available[0]->getStock(),
                'locale' => $this->session->get('_locale')->getName()
                )
            :

            $response = array(
                'status' => 1,
                'message' => 'ok',
                'data' => 'success',
                'event' => $event->getEvent(),
                'blocked'=> $blockingDate,
                'blocktype'=> $blockedDates->getOnlyDates(),
                'minDate' => $minDate,
                'available' => $available[0]->getStock(),
                'locale' => $this->session->get('_locale')->getName()
            );

        return new JsonResponse($response);
    }

 }
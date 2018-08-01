<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Blockdates;
use App\Entity\Event;
use App\Entity\Category;

class BookingController extends AbstractController
{

 public function getBlockedDatesCategory(Request $request)
    {   
        $categoryId = $request->request->get('id');

        $blockingDate = array();
        
        $now = new \Datetime('now');
        
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
                'data' => 'fail',
                'event' => '',
                'blocked'=>'',
                'blocktype'=>'',
                'minDate' => '')
            :

            $response = array(
                'data' => 'success',
                'event' => $event->getEvent(),
                'blocked'=> $blockingDate,
                'blocktype'=> $blockedDates->getOnlyDates(),
                'minDate' => $minDate
            );

        return new JsonResponse($response);
    }

 }
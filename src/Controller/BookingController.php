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
use App\Service\RequestInfo;

class BookingController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }

    public function getAvailable(Request $request, RequestInfo $reqInfo)
    {

        $err = array();
        
        $totalPax = 0;
        
        !$this->session->get('_locale') ? $this->session->set('_locale', 'pt_PT') : false;

        $locale = $reqInfo->getBrownserLocale($request);

        $categoryId = $request->request->get('category') ? $request->request->get('category') : $err[] = 'TOUR';
        
        $adult = $request->request->get('adult') || $request->request->get('adult') === "0" ? $request->request->get('adult') : $err[] = 'ADULT';
        
        $children = $request->request->get('children') || $request->request->get('children') === "0" ? $request->request->get('children') : $err[] = 'CHILDREN';
        
        $baby = $request->request->get('baby') || $request->request->get('baby') === "0" ? $request->request->get('baby') :  $err[] = 'BABY';
        
        $totalPax = (int)$baby + (int)$children + (int)$adult == 0 ? $err[] = 'ZERO' : (int)$baby + (int)$children + (int)$adult;
        
        if ((int)$adult < 1) $err[] = 'ZERO';

        //user didnt fill the necessary fields send back info
        if ($err) {
            $response = array(
                'status' => 0,
                'message' => $err,
                'minDate' => null,
                'available' => null,
            );
            return new JsonResponse($response);
        }

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($categoryId);

        //min date we start the search is tomorrow, so has the min date available in datepicker
        $startDt = new \DateTime('tomorrow');
        
        //prevent if category not found, return back info
        if(!$category){
            $response = array(
                'status' => 2,
                'wp' => null,
                'message' => 'NO_STOCK',
                'max' => null,
                'minDate' => null,
                'available' => null,
            );
            return new JsonResponse($response);
        }

        $available = $em->getRepository(Available::class)->findByCategoryDateTomorrow($category, $startDt->format('Y-m-d H:i:s'), $totalPax);

        $stockAvailable = array();

        //user over max lotation send back info
        if ($totalPax > $category->getAvailability()){
            $response = array(
            'status' => 2,
            'wp' => null,
            'message' => 'NO_STOCK',
            'max' => '(Máx: '.$category->getAvailability().' Pax)',
            'minDate' => null,
            'available' => null,
            );

        return new JsonResponse($response);
        }

        //category has stock
        if($available){
            //send the stock back 
            foreach ($available as $stock)
                $stockAvailable[] = array(
                    'id' => $stock->getId(),
                    'datetime' => $stock->getDatetimeStart()->format('Y-m-d H:i'),
                    'date' => $stock->getDatetimeStart()->format('Y-m-d'),
                    'time' => $stock->getDatetimeStart()->format('H:i'),
                    'stock'=> $stock->getStock(),
                    'lotation' => $stock->getLotation(),
                    'onlyLeft' => $stock->getLotation() * 0.25 > $stock->getStock() ? $stock->getStock() : null 
                );
            
            $minDate = $stockAvailable[0]['date'] >= $startDt->format('Y-m-d') ? $stockAvailable[0]['date'] : $startDt->format('Y-m-d') ;
            
            //since we have stock, set the expiration time to purchase ticket 
            $this->session->set('start_time', $request->server->get('REQUEST_TIME'));

            $response = array(
            'status' => 1,
            'wp' => $category->getWarrantyPayment(),
            'message' => count($available),
            'max'=> null,
            'expiration' => 900,
            'minDate' => $minDate,
            'available' => $stockAvailable,
            );
        }

        //category no stock send back info
        else
            $response = array(
            'status' => 2,
            'wp' => null,
            'message' => 'NO_STOCK',
            'minDate' => null,
            'available' => null,
            );

        return new JsonResponse($response);
    }

 }
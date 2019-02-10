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
        $baby = $request->request->get('baby') || $request->request->get('baby') === "0" ? $request->request->get('baby') :  $err[] = 'BABY';
        $totalPax = (int)$baby + (int)$children + (int)$adult == 0 ? $err[] = 'ZERO' : (int)$baby + (int)$children + (int)$adult;
        if ((int)$adult < 1) $err[] = 'ZERO';

        //user didnt fill teh necessary fields send back info
        if ($err) {
            $response = array(
                'status' => 0,
                'message' => $err,
                'blocktype'=> null,
                'minDate' => null,
                'available' => null,
                'locale' => $this->session->get('_locale')->getName()
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
                'message' => 'NO_STOCK',
                'max' => null,
                'minDate' => null,
                'available' => null,
                'locale' => $this->session->get('_locale')->getName()
            );
            return new JsonResponse($response);
        }

        $available = $em->getRepository(Available::class)->findByCategoryDateTomorrow($category, $startDt->format('Y-m-d H:i:s'), $totalPax);

        $stockAvailable = array();

        //user over max lotation send back info
        if ($totalPax > $category->getAvailability()){
            $response = array(
            'status' => 2,
            'message' => 'NO_STOCK',
            'max' => '(Máx: '.$category->getAvailability().' Pax)',
            'minDate' => null,
            'available' => null,
            'locale' => $this->session->get('_locale')->getName()
            );

        return new JsonResponse($response);
        }

        //category has stock
        if($available){
            //send the stock back 
            foreach ($available as $stock)
                $stockAvailable[] = array(
                    'id' => $stock->getId(),
                    'datetime' => $stock->getDatetime()->format('Y-m-d H:i'),
                    'date' =>$stock->getDatetime()->format('Y-m-d'),
                    'time' => $stock->getDatetime()->format('H:i'),
                    'stock'=> $stock->getStock(),
                    'lotation' => $stock->getLotation(),
                    'onlyLeft' => $stock->getLotation() * 0.25 > $stock->getStock() ? $stock->getStock() : null 
                );
            
            $response = array(
            'status' => 1,
            'message' => count($available),
            'max'=> null,
            'minDate' => $startDt->format('Y-m-d H:i:s'),
            'available' => $stockAvailable,
            'locale' => $this->session->get('_locale')->getName()
            );
        }

        //category no stock send back info
        else
            $response = array(
            'status' => 2,
            'message' => 'NO_STOCK',
            'blocktype'=> 0,
            'minDate' => null,
            'available' => null,
            'locale' => $this->session->get('_locale')->getName()
            );

        return new JsonResponse($response);
    }

 }
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Blockdates;
use App\Entity\Event;
use App\Entity\Category;
use App\Entity\Available;


class BookingController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }


    public function getAvailable(Request $request,TranslatorInterface $translator)
    {

        $err = [];
        
        $totalPax = 0;

        $categoryId = $request->request->get('category') ? $request->request->get('category') : $err[] = $translator->trans('part_seven.tour');
        $adult = $request->request->get('adult') || $request->request->get('adult') == '0' ? $request->request->get('adult') : $err[] = $translator->trans('part_seven.adult');
        $children = $request->request->get('children') || $request->request->get('children') == '0' ? $request->request->get('children') : 0;
        $baby = $request->request->get('baby') || $request->request->get('baby') == 0 ? $request->request->get('baby') : 0;
                
        if ($err)
            return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'minDate' => null,
                'available' => null
                ]);

        $totalPax = (int)$baby + (int)$children + (int)$adult;
        
        if ((int)$adult < 1)
            $err[] = $translator->trans('part_seven.zero');

        //user didnt fill the necessary fields send back info

        if ($err)
            return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'minDate' => null,
                'available' => null
            ]);

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($categoryId);

        //min date we start the search is tomorrow, so has the min date available in datepicker
        $startDt = new \DateTime('tomorrow', new \DateTimeZone('Europe/Lisbon'));
        $now = new \DateTime('now', new \DateTimeZone('Europe/Lisbon'));
        //prevent if category not found, return back info
        if(!$category)
            return new JsonResponse([
                'status' => 2,
                'message' => $translator->trans('part_seven.no_stock'),
            ]);

        $available = $em->getRepository(Available::class)->findByCategoryDateTomorrow($category, $startDt->format('Y-m-d H:i:s'), $totalPax);

        $stockAvailable = [];

        //user over max lotation send back info
        if ($totalPax > $category->getAvailability())
            return new JsonResponse([
                'status' => 2,
                'message' =>  $translator->trans('part_seven.no_stock').'<br>(Máx: '.$category->getAvailability().' Pax)',
            ]);

        //category has stock
        if($available){
            //send the stock back 
            foreach ($available as $stock)
                $stockAvailable[] = [
                    'id' => $stock->getId(),
                    'datetime' => $stock->getDatetimeStart()->format('Y-m-d H:i'),
                    'date' => $stock->getDatetimeStart()->format('Y-m-d'),
                    'time' => $stock->getDatetimeStart()->format('H:i'),
                    'stock'=> $stock->getStock(),
                    'lotation' => $stock->getLotation(),
                    'onlyLeft' => $stock->getLotation() * 0.25 > $stock->getStock() ? $stock->getStock() : null 
                ];
            
            $minDate = $stockAvailable[0]['date'] >= $startDt->format('Y-m-d') ? $stockAvailable[0]['date'] : $startDt->format('Y-m-d') ;
            
            //since we have stock, set the expiration time to purchase ticket 
            $this->session->set('start_time', $request->server->get('REQUEST_TIME'));

            return new JsonResponse([
                'status' => 1,
                'wp' => $category->getWarrantyPayment(),
                'message' => count($available),
                'max'=> null,
                'expiration' => 900,
                'minDate' => $minDate,
                'available' => $stockAvailable,
                'minDateTime' =>  $startDt->format('Y-m-d H:i:s'),
                'nowDateTime' => $now->format('Y-m-d H:i:s')
            ]);
        }

        //category no stock send back info
        return new JsonResponse([
            'status' => 2,
            'message' => $translator->trans('part_seven.no_stock')
        ]);
    }

}
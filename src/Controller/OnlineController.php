<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Stripe;
use App\Entity\Company;
use App\Entity\Booking;
use App\Entity\StripePaymentLogs;
use App\Entity\StripeRefundLogs;
use App\Service\RequestInfo;

class OnlineController extends AbstractController
{

    public function index(Stripe $stripe, Request $request, TranslatorInterface $translator, RequestInfo $reqInfo)
    {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);
        $booking = $em->getRepository(Booking::class)->find($id);

        $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy(['booking' => $booking]); 

        return $this->render('admin/pay-online.html',
            [
                'booking' => $booking,
                'company' => $company,
                'menus' => [],//$menu->adminMenu(),
                'paylog' => $paylog,
                'payment_intent' => $stripe->createUpdatePaymentIntent($company, $request, $booking),
                'reasons' => $stripe->refundReasons($translator),
                'host' => $reqInfo->getHost($request)
            ]);
    }

    /**
    *Create Charge
    *@param $request
    *@return json response of Request
    **/
    public function chargeCreditCard(Request $request, Stripe $stripe)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        
        $company = $em->getRepository(Company::class)->find(1);
        
        $booking = $em->getRepository(Booking::class)->find(1);

        $i = $stripe->createUpdatePaymentIntent($company, $request, $booking);

        return new JsonResponse([
            'status' => 1,
            'message' => 'success',
            'data' => $i
        ]);
    }

    /**
    *Get the receipt url to show on email
    *@param $request
    *@return json response of Request
    **/
    public function onlineGetCharge(Request $request, Stripe $stripe)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $ch = $stripe->getPaymentCharge($company, $request);

        $b_id = explode('-', $ch['data']->data[0]->description);
        
        $booking = $em->getRepository(Booking::class)->find(str_replace('#','',$b_id[0]));

        $paylog = new StripePaymentLogs();

        $paylog->setLog(json_encode($ch['data']->data[0]));
        $paylog->setBooking($booking);
        $em->persist($paylog);
        $em->flush();

        return new JsonResponse([
            'status' => 1,
            'message' => 'success',
            'data' => $ch]);
    }


    public function refund(Stripe $stripe, Request $request, TranslatorInterface $translator, RequestInfo $reqInfo)
    {
        
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);
        $booking = $em->getRepository(Booking::class)->find($id);

        $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy(['booking' => $booking]); 

        return $this->render('admin/refund-online.html',
            [
                'booking' => $booking,
                'company' => $company,
                'menus' => [],//$menu->adminMenu(),
                'paylog' => $paylog,
                'reasons' => $stripe->refundReasons($translator),
                'host' => $reqInfo->getHost($request)
            ]);
    }

    /**
    *Do a refund to the client
    *@param 
    *@return json response of Request
    **/
    public function onlinePaymentRefund(Request $request, Stripe $stripe)
    {
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $booking = $em->getRepository(Booking::class)->find($request->request->get('b_id'));

        $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy( ['booking' => $booking ] ); 

        $rfd = $stripe->createRefund($company, $request, $paylog->getlogObj()->id);

        $reflog = new StripeRefundLogs();


        if ($rfd['status'] == 0) {

            $reflog->setLog(json_encode($rfd['message']));
            $reflog->setBooking($booking);
            $em->persist($reflog);
            $em->flush();
            
            return new JsonResponse([
                'status' => 0,
                'message' => $rfd['data'],
                'data' => $rfd['message']
            ]);  
        }

        $reflog->setLog(json_encode($rfd['data']));
        $reflog->setBooking($booking);
        $em->persist($reflog);
        $em->flush();
        
        return new JsonResponse([
            'status' => 1,
            'message' => 'success',
            'data' => $rfd['data']->data->status
        ]);
    }


}
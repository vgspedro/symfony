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

        if($id){

            $em = $this->getDoctrine()->getManager();
            $company = $em->getRepository(Company::class)->find(1);
            $booking = $em->getRepository(Booking::class)->find($id);

            $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy(['booking' => $booking]);

            $text = [
                'payment' => $translator->trans('payment', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'purchase_data' => $translator->trans('purchase_data', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'tour' => $translator->trans('tour', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'status' => $translator->trans('status', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'date' => $translator->trans('date', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'name' => $translator->trans('name', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'adults' => $translator->trans('adults', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'childrens' => $translator->trans('childrens', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'babies' => $translator->trans('babies', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'amount' => $translator->trans('amount', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'insert_card_n' => $translator->trans('insert_card_n', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'pay_now' => $translator->trans('pay_now', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'booking_status' => $translator->trans($booking->getStatus(), array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'payment_status' => $translator->trans($booking->getPaymentStatus(), array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'error' => $translator->trans('error', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'check' => $translator->trans('check', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'success' => $translator->trans('success', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'wifi_error' => $translator->trans('wifi_error', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'receipt' => $translator->trans('receipt', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            ];

            return $this->render('admin/pay-online.html',
                [
                    'booking' => $booking,
                    'company' => $company,
                    'paylog' => $paylog,
                    'translator' => $text,
                    'payment_intent' => $stripe->createUpdatePaymentIntent($company, $request, $booking),
                    'reasons' => $stripe->refundReasons($translator),
                    'host' => $reqInfo->getHost($request)
                ]);
        }
        else
            return $this->redirectToRoute('index');
    }

    /**
    *Create Charge
    *@param $request
    *@return json response of Request
    **/
    public function chargeCreditCard(Request $request, Stripe $stripe, TranslatorInterface $translator)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();
        
        $company = $em->getRepository(Company::class)->find(1);
        
        $booking = $em->getRepository(Booking::class)->find($id);

        $i = $stripe->createUpdatePaymentIntent($company, $request, $booking);

            $text = [
                'receipt' => $translator->trans('receipt', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            ];

        return new JsonResponse([
            'status' => 1,
            'message' => 'success',
            'data' => $i,
        ]);
    }

    /**
    *Get the receipt url to show on email
    *@param $request
    *@return json response of Request
    **/
    public function onlineGetCharge(Request $request, Stripe $stripe, TranslatorInterface $translator)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $ch = $stripe->getPaymentCharge($company, $request);

        $b_id = explode('-', $ch['data']->data[0]->description);
        
        $booking = $em->getRepository(Booking::class)->find(str_replace('#','',$b_id[0]));

        $paylog = new StripePaymentLogs();

        $paylog->setLog(json_encode($ch['data']->data[0]));

        $booking->setPaymentStatus(Booking::STATUS_SUCCEEDED);
        
        $paylog->setBooking($booking);
        
        $em->persist($paylog);
        
        $em->flush();

        return new JsonResponse([
            'status' => 1,
            'message' => [
                'text' => $translator->trans('payment_txt', array(), 'messages', $booking->getClient()->getLocale()->getName()), 
                'status' => $translator->trans( $booking->getPaymentStatus(), array(), 'messages', $booking->getClient()->getLocale()->getName())
            ],
            'data' => $ch] );
    }


    public function refund(Stripe $stripe, Request $request, TranslatorInterface $translator, RequestInfo $reqInfo)
    {
        
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);

        $booking = $em->getRepository(Booking::class)->find($id);

        $text = [
            'payment' => $translator->trans('payment', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'purchase_data' => $translator->trans('purchase_data', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'tour' => $translator->trans('tour', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'status' =>  $translator->trans('status', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'date' =>  $translator->trans('date', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'name' =>  $translator->trans('name', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'adults' =>  $translator->trans('adults', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'childrens' =>  $translator->trans('children', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'babies' =>  $translator->trans('babies', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'amount' =>  $translator->trans('amount', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'refund' =>  $translator->trans('refund', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'motive' => $translator->trans('motive', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'booking_status' => $translator->trans($booking->getStatus(), array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'payment_status' => $translator->trans($booking->getPaymentStatus(), array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'error' => $translator->trans('error', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'check' => $translator->trans('check', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'success' => $translator->trans('success', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'wifi_error' => $translator->trans('wifi_error', array(), 'messages', $booking->getClient()->getLocale()->getName()),
            'receipt' => $translator->trans('receipt', array(), 'messages', $booking->getClient()->getLocale()->getName()),
        ];

        $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy(['booking' => $booking]); 

        return $this->render('admin/refund-online.html',
            [
                'booking' => $booking,
                'company' => $company,
                'paylog' => $paylog,
                'translator' => $text,
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
<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Stripe;
use App\Entity\Company;
use App\Entity\Locales;
use App\Entity\ExtraPayment;
use App\Service\RequestInfo;
use Money\Money;

class ExtraPaymentController extends AbstractController
{

    public function createPayment(Stripe $stripe, Request $request, TranslatorInterface $translator, RequestInfo $reqInfo)
    {

        $em = $this->getDoctrine()->getManager();
        
        $company = $em->getRepository(Company::class)->find(1);

        $local = $request->getLocale();

        $locale = $em->getRepository(Locales::class)->findOneby(['name' => $local]);

        $text = [
            'required' => $translator->trans('required', array(), 'messages', $locale ->getName()), 
            'next' => $translator->trans('next', array(), 'messages', $locale ->getName()), 
            'description' => $translator->trans('description', array(), 'messages', $locale ->getName()), 
            'payment' => $translator->trans('payment', array(), 'messages', $locale ->getName()),
            'phone' => $translator->trans('phone', array(), 'messages', $locale ->getName()),
            'name' => $translator->trans('name', array(), 'messages', $locale->getName()),
            'amount' => $translator->trans('amount', array(), 'messages', $locale ->getName()),
            'insert_card_n' => $translator->trans('insert_card_n', array(), 'messages', $locale ->getName()),
            'pay_now' => $translator->trans('pay_now', array(), 'messages',$locale ->getName()),
            'error' => $translator->trans('error', array(), 'messages', $locale ->getName()),
            'check' => $translator->trans('check', array(), 'messages', $locale ->getName()),
            'success' => $translator->trans('success', array(), 'messages', $locale ->getName()),
            'wifi_error' => $translator->trans('wifi_error', array(), 'messages', $locale ->getName()),
            'receipt' => $translator->trans('receipt', array(), 'messages', $locale ->getName()),
        ];
        
        return $this->render('admin/create-pay-online.html',
            [
                'company' => $company,
                'translator' => $text,
                'payment_intent' => $stripe->createUpdatePaymentIntent($company, $request, null),
                'host' => $reqInfo->getHost($request)
            ]);
        //else
        //    return $this->redirectToRoute('index');
    }


    public function list(Stripe $stripe, Request $request, TranslatorInterface $translator, RequestInfo $reqInfo)
    {

        $id = $request->request->get('id');

        if($id){
        
            $em = $this->getDoctrine()->getManager();
            $booking = $em->getRepository(Booking::class)->find($id);
            $company = $em->getRepository(Company::class)->find(1);
            $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy(['booking' => $booking]);

            $text = [
                'payment' => $translator->trans('payment', array(), 'messages', $booking->getClient()->getLocale()->getName()),
                'phone' => $translator->trans('phone', array(), 'messages', $booking->getClient()->getLocale()->getName()),
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
                    'host' => $reqInfo->getHost($request)
                ]);
        }
        else
            return $this->redirectToRoute('index');
    }

}
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Service\Stripe;
use App\Entity\Company;
use App\Entity\Booking;
use App\Entity\Locales;
use App\Entity\StripePaymentLogs;

use App\Service\RequestInfo;
use App\Service\MoneyParser;
use App\Service\EmailSender;

use Money\Money;

class OnlineController extends AbstractController
{

    private $emailer;

    public function __construct(EmailSender $emailer)
    {
        $this->emailer = $emailer;
    }


    public function index(Request $request, TranslatorInterface $translator)
    {
        $id = $request->request->get('id');

        $index = $request->request->get('index');

        $em = $this->getDoctrine()->getManager();

        $booking = $em->getRepository(Booking::class)->find($id);

        if(!$booking)
            return new JsonResponse([
                'status' => 0,
                'message' => 'A Reserva #'.$id.' não foi encontrada!',
                'data' => null
            ]);



        // only allow payments of bookings where payment status is canceled, from today to past events, prevent problems in stock in future bookings

        $now = new \DateTime();

         if ($booking->getPaymentStatus() == 'canceled' && $booking->getAvailable()->getDatetimeStart()->format('Y-m-d') >= $now->format('Y-m-d')){
            return new JsonResponse([
                'status' => 0,
                'message' => 'Não é possivel efetuar pagamento deste reserva!<br> A Data de realização '.$booking->getAvailable()->getDatetimeStart()->format('d/m/Y').' tem que ser anterior ou igual à data actual '.$now->format('d/m/Y'),
                'data' => null
            ]);
        }

        $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy(['booking' => $booking]);

        // a payment on this boolinkg have been done already
        if($paylog)
            return new JsonResponse([
                'status' => 0,
                'message' => 'Pagamento já foi efetuado, clique em "Procurar"',
                'data' => [ 'status' => $paylog->getLogObj()->status, 'index' => $index ]
            ]);



        return new JsonResponse([
            'status' => 1,
            'message' => 'redirect to Payment.',
            'data' => null
        ]);    
    }


    public function setPayment(Stripe $stripe, Request $request, TranslatorInterface $translator, RequestInfo $reqInfo)
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

    /**
    *Create Charge
    *@param $request
    *@return json response of Request
    **/
    public function chargeCreditCard(Request $request, Stripe $stripe, TranslatorInterface $translator)
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        $id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();
        
        $company = $em->getRepository(Company::class)->find(1);
        $booking = $em->getRepository(Booking::class)->find($id);

        $i = $stripe->createUpdatePaymentIntent($company, $request, $booking);

        return $i['status'] == 1 ? 
            new JsonResponse([
                'status' => 1,
                'message' => 'success',
                'data' => $i,
            ]) :
            new JsonResponse([
                'status' => 0,
                'message' => 'Unable to Charge Credit Card',
                'data' => null]);
    }


    /**
    *Get the receipt url to show on email, update/create StripePaymentLog Obj
    *@param $request
    *@return json response of Request
    **/
    public function onlineGetCharge(Request $request, Stripe $stripe, TranslatorInterface $translator)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $ch = $stripe->getPaymentCharge($company, $request);

        if($ch['status'] == 1){
            $b_id = explode('-', $ch['data']->data[0]->description);
            $deposit = Money::EUR($ch['data']->data[0]->amount);

            $booking = $em->getRepository(Booking::class)->find(str_replace('#','',$b_id[0]));

            //just create a payment for in admin zone new StripePaymentLogs 
            if(!$booking->getStripePaymentLogs()){

                $payLogs = new StripePaymentLogs();
                $payLogs->setLog(json_encode($ch['data']->data[0]));
                $payLogs->setBooking($booking);
                $booking->setStripePaymentLogs($payLogs);

            }

            //online booking the StripePaymentLogs already exists, just update the log
            else
                $booking->getStripePaymentLogs()->setLog(json_encode($ch['data']->data[0]));
            
            $booking->setPaymentStatus($ch['data']->data[0]->status);
            $booking->setDepositAmount($deposit);
            $em->persist($booking);
            $em->flush();

            return new JsonResponse([
                'status' => 1,
                'message' => [
                    'text' => $translator->trans('payment_txt', array(), 'messages', $booking->getClient()->getLocale()->getName()), 
                    'status' => $translator->trans( $booking->getPaymentStatus(), array(), 'messages', $booking->getClient()->getLocale()->getName())
                ],
                'data' => $ch]);
            }

        else
            return new JsonResponse([
                'status' => 0,
                'message' => 'Unable to Charge the Amount!',
                'data' => null]);
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
            'childrens' =>  $translator->trans('childrens', array(), 'messages', $booking->getClient()->getLocale()->getName()),
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
    public function onlinePaymentRefund(Request $request, Stripe $stripe, TranslatorInterface $translator, MoneyParser $moneyParser)
    {
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $id = $request->request->get('id');

        if(!$request->request->get('amount'))

            return new JsonResponse([  
                'status' => 0,
                'message' => 'Insira montante a reembolsar!',
                'data' => null
            ]);

        $charge = $moneyParser->parse($request->request->get('amount'));
        
        //just to avoid 0 refund or a max refund of 5000
        if($charge->getAmount() < 1 || $charge->getAmount() > 500000)

            return new JsonResponse([  
                'status' => 0,
                'message' => 'Montante Min: 0.01 - Max: 5000.00' ,
                'data' => null
            ]);


        $booking = $em->getRepository(Booking::class)->find($id);

        if(!$booking)
            return new JsonResponse([
                'status' => 0,
                'message' => 'Reserva #'.$id,
                'data' => null
        ]);  


        $paylog = $em->getRepository(StripePaymentLogs::class)->findOneBy( ['booking' => $booking ] ); 

        if(!$paylog)

            return new JsonResponse([
                'status' => 0,
                'message' => 'Pagamento da Reserva #'.$id.' não encontrado',
                'data' => null
            ]);  

        $rfd = $stripe->createRefund($company, $request, $paylog->getlogObj()->id);

        if ($rfd['status'] == 0) {
            
            return new JsonResponse([
                'status' => 0,
                'message' => $rfd['data'],
                'data' => $rfd['message']
            ]);  
        }

        $c_update = $stripe->retrieveCharge($company, $paylog->getlogObj()->id);

        $booking->getStripePaymentLogs()->setLog(json_encode($c_update['data']));

        if($c_update['data']->amount_refunded > 0){
            $status = $c_update['data']->amount != $c_update['data']->amount_refunded ? Booking::STATUS_PARTIAL_REFUND: Booking::STATUS_REFUNDED; 
            $booking->setPaymentStatus($status);
        }

        $em->persist($booking);
        $em->flush();
        
        if($booking->getClient()->getEmail()){

            //Send email to client
            $send = $this->emailer->sendBookingRefund($company, $booking);

            return $send['status'] == 1 ?
                new JsonResponse([ 
                    'status' => 1,
                    'message' => 'Reembolso efetuado e email enviado ao Cliente.',
                    'data' => [
                        'txt' => $translator->trans($booking->getPaymentStatus()),
                        'status' => $booking->getPaymentStatus(),
                        'email' => $send]
                    ])
                :
                new JsonResponse([
                    'status' => 1,
                    'message' => 'Reembolso efetuado, mas email não foi enviado ao Cliente!',
                    'data' => [
                        'txt' => $translator->trans($booking->getPaymentStatus()),
                        'status' => $booking->getPaymentStatus(),
                        'email' => $send]
                    ]);

        }

        return new JsonResponse([
            'status' => 1,
            'message' => 'Reembolso efetuado, mas o cliente não tem email associado, logo email não foi enviado',
            'data' => [
                'txt' => $translator->trans($booking->getPaymentStatus()),
                'status' => $booking->getPaymentStatus(),
                'email' => $send
            ]
        ]);






    }

    /**
     * @param Request $request operator data  
     */
    public function paymentLogs(Request $request, TranslatorInterface $translator, Stripe $stripe){

        $id = $request->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);

        if(!$company){
            return new JsonResponse([
                    'status' => 0,
                    'message' => 'fail',
                    'data' => 'Stripe Credencials not found.'
                ]);
        }

        $booking = $em->getRepository(Booking::class)->find($id);        
        //INFORM THE USER BOOKING NOT FOUND 
        if(!$booking){
          return new JsonResponse([
            'status' => 0,
            'message' => 'fail',
            'data' => 'Booking # '.$id.' not found']);
        }

        if($booking->getStripePaymentLogs() && $booking->getStripePaymentLogs()->getLogObj()){

            $charge_id = $booking->getStripePaymentLogs()->getLogObj()->id;


            if($booking->getPaymentStatus() == 'canceled'){
                $logs = $stripe->getPaymentChargeCanceled($company,$charge_id);

                if ($logs['status'] == 1)
                    return $this->render('admin/pay-logs-canceled.html',[
                        'logs' => $logs['data'],
                        'booking' => $booking
                    ]);
                
                return $this->render('admin/pay-logs-canceled.html',[
                    'logs' => ['data' => null],
                    'booking' => $booking
                ]);
            }

            $logs = $stripe->retrieveCharge($company, $charge_id);

            $booking->getStripePaymentLogs()->setLog(json_encode($logs['data']));

            if($logs['data']->amount_refunded > 0){
                $status = $logs['data']->amount != $logs['data']->amount_refunded ? Booking::STATUS_PARTIAL_REFUND: Booking::STATUS_REFUNDED; 
                $booking->setPaymentStatus($status);
            }

            $em->persist($booking);
            $em->flush();

            if ($logs['status'] == 1)
                return $this->render('admin/pay-logs.html',[
                    'logs' => $logs['data'], 
                    'booking' => [
                        'txt' =>$translator->trans($booking->getPaymentStatus()),
                        'status' => $booking->getPaymentStatus()
                    ]
                ]);

            else
                return new JsonResponse($logs);
        }

        return $this->render('admin/pay-logs-canceled.html',[
            'logs' => ['data' => null],
            'booking' => $booking
            ]);
    }

}
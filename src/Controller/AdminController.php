<?php
namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Category;
use App\Entity\Blockdates;
use App\Entity\Booking;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\EasyText;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use App\Form\CategoryType;
use App\Form\GalleryType;
//use App\Form\BlockdatesType;
use App\Form\EventType;
use App\Form\EasyTextType;
use App\Service\MoneyFormatter;
use App\Service\MoneyParser;
use App\Service\Menu;
use App\Service\RequestInfo;
use Money\Money;

class AdminController extends AbstractController
{

    public function html(Request $request, RequestInfo $reqInfo, Menu $menu)
    {
        $em = $this->getDoctrine()->getManager();
        $booking = $em->getRepository(Booking::class)->dashboardValues();

        $start = new \DateTime('first day of this month');
        $end = new \DateTime('last day of this month');
        $booking_month = $em->getRepository(Booking::class)->dashboardCurrentMonth($start, $end);

        $company = $em->getRepository(Company::class)->find(1);
        
        return $this->render('admin/base.html.twig',[
            'menus' => $menu->administration(),
            'browser' => $reqInfo->browserInfo($request),
            'booking' => $booking,
            'booking_month' => $booking_month,
            'company' => $company,
            'host' => $reqInfo->getHost($request),
            'url' => 'https://'.$reqInfo->getHost($request)

        ]);
    }

	public function adminDashboard()
	{
        $em = $this->getDoctrine()->getManager();
        $booking = $em->getRepository(Booking::class)->dashboardValues();
        $start = new \DateTime('first day of this month');
        $end = new \DateTime('last day of this month');
        $booking_month = $em->getRepository(Booking::class)->dashboardCurrentMonth($start, $end);
        return $this->render('admin/dashboard.html', 
            [
                'booking' => $booking,
                'booking_month' => $booking_month
            ]);
    }

    public function adminBookingSetStatus(Request $request){

        $bookingId = $request->request->get('id');
        $index =  $request->request->get('index');
        $em = $this->getDoctrine()->getManager();
                
        $booking = $em->getRepository(Booking::class)->find($bookingId);
        $easyText = $em->getRepository(EasyText::class)->findAll();

        $client = $booking->getClient();

        $seeBooking =
            [
                'booking' => $booking->getId(),
                'adult' => $booking->getAdult(),
                'children' => $booking->getChildren(),
                'baby' => $booking->getBaby(),
                'status' => $booking->getStatus(),
                'date' => $booking->getDateEvent()->format('d/m/Y'),
                'hour' => $booking->getTimeEvent()->format('H:i'),
                'tour' => $booking->getAvailable()->getCategory()->getNamePt(),
                'notes' => $booking->getNotes(),
                'user_id' => $client->getId(),   
                'username' => $client->getUsername(),
                'address' => $client->getAddress(),
                'email' => $client->getEmail(),          
                'telephone' => $client->getTelephone(),
                'wp' => $client->getCvv() ? 1 : 0, 
                'language' => $client->getLocale()->getName(),
                'easyText' => $easyText,
                'index' => $index
            ];          

        return $this->render('admin/booking-set-status.html', array('seeBooking' => $seeBooking));
    }


    public function adminBookingSendStatus(Request $request, TranslatorInterface $translator){

        $em = $this->getDoctrine()->getManager();
                
        $bookingId = $request->request->get('bookingstatusId');
        $status = strtolower($request->request->get('status'));
        $email = $request->request->get('email');
        $notes = $request->request->get('notes');
        $index = $request->request->get('index');
        
        $booking = $em->getRepository(Booking::class)->find($bookingId);

        //if booking not found send info back to user
        if(!$booking)
            return new JsonResponse([
                'status' => 0,
                'message' => 'Reserva não encontrada',
                'data' => null,
                'mail' => null
            ]);

        //if order canceled and previous status is not canceled lets put the stock back in the available
        $stockIt = 0;
        if(strtolower($status) == 'canceled' && strtolower($booking->getStatus()) != 'canceled'){
            
            $stockIt = 1;
            $booking->getAvailable()->setStock((int)$booking->getAvailable()->getStock() + (int)$booking->getCountPax());
        
        }

        $company = $em->getRepository(Company::class)->find(1);

        $booking->setStatus($status);
        $booking->setNotes($notes);

        $client = $booking->getClient();
        //only change the cleint email if is diferent form the request
        //some mail could be wrong 
        
        if($booking->getClient()->getEmail() != $email)
            $client->setEmail($email);
        
        $em->flush();

        $categoryName = $client->getLocale()->getName() =='en' ? $booking->getAvailable()->getCategory()->getNameEn() : 
            $booking->getAvailable()->getCategory()->getNamePt();

        $seeBooking =
            [
                'id' => $booking->getId(),
                'adult' => $booking->getAdult(),
                'children' => $booking->getChildren(),
                'baby' => $booking->getBaby(),
                'status' => strtoupper($translator->trans($booking->getStatus(), [], 'messages', $booking->getClient()->getLocale()->getName())),
                'date' => $booking->getDateEvent()->format('d/m/Y'),
                'hour' => $booking->getTimeEvent()->format('H:i'),
                'tour' => $categoryName,
                'notes' => $booking->getNotes(),
                'user_id' => $client->getId(),   
                'username' => $client->getUsername(),
                'logo' => 'https://'.$request->getHost().'/upload/gallery/'.$company->getLogo(),
                'company_name' => $company->getName()
            ];          

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());

        $mailer = new \Swift_Mailer($transport);

        $subject =  $translator->trans('booking', [], 'messages', $booking->getClient()->getLocale()->getName()).'#'.$booking->getId().' ('.strtoupper($translator->trans($booking->getStatus(), [], 'messages', $booking->getClient()->getLocale()->getName())).')';

        $message = (new \Swift_Message($subject))
            ->setFrom([$company->getEmail() => $company->getName()])
            ->setTo([$client->getEmail() => $client->getUsername(), $company->getEmail() => $company->getName()])
            ->addPart($subject, 'text/plain')
            ->setBody($this->renderView(
                'emails/booking-status-'.$client->getLocale()->getName().'.html.twig',$seeBooking
                ),
                'text/html'
            );
                        
        $send = $mailer->send($message);
        
        return new JsonResponse([
            'status' => 1,
            'message' => 'Sucesso',
            'data' => ['id' => $booking->getId(), 'index' => (int)$index, 'status' => strtoupper($booking->getStatus())],
            'mail' => $send,
            'stock_it' => $stockIt
        ]);
    }


    public function adminBooking(Request $request, TranslatorInterface $translator)
    {
        $status[] = ['color' =>'w3-red', 'name' => 'pending', 'action' => strtoupper($translator->trans('pending'))];
        $status[] = ['color' =>'w3-blue', 'name' => 'canceled', 'action' => strtoupper($translator->trans('canceled'))];
        $status[] = ['color' =>'w3-green', 'name' => 'confirmed', 'action' => strtoupper($translator->trans('confirmed'))];
        $status[] = ['color' =>'w3-black', 'name' => 'total', 'action' => ''];

        $table=['Reserva','Acções','Tour','Data','Hora','Adulto','Criança','Bébé','Depósito', 'Total' ,'Pagamento','Notas','Cliente','Email','Morada','Telefone','Compra','W.P.'];

        return $this->render('admin/booking.html', ['status' => $status, 'table' => $table]);
    }


    public function adminBookingSearch(Request $request, MoneyFormatter $moneyFormatter, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();

        $start = $request->query->get('startDate') ? date_create_from_format("d/m/Y", $request->query->get('startDate')) : null; 
        $end = $request->query->get('endDate') ? date_create_from_format("d/m/Y", $request->query->get('endDate')) : null; 

        $start = $start != null ? $start->format('Y-m-d') : null;
        $end = $end != null ? $end->format('Y-m-d') : null;

    if ($start || $end){

        $canceled = 0;
        $pending = 0;
        $confirmed = 0;

        $bookings = $this->getDoctrine()->getManager()->getRepository(Booking::class)->bookingFilter($start, $end);

        if ($bookings){

            foreach ($bookings as $booking) {

                if ($booking->getStatus() ==='canceled')
                    $canceled = $canceled+1;
                else if ($booking->getStatus() ==='pending')
                    $pending = $pending+1;
                else if ($booking->getStatus() ==='confirmed')
                    $confirmed = $confirmed+1;
                
                $client = $booking->getClient();
                
                $seeBookings[] =
                    [
                        'id' => $booking->getId(),
                        'adult' => $booking->getAdult(),
                        'children' => $booking->getChildren(),
                        'baby' => $booking->getBaby(),
                        'status' => $booking->getStatus(),
                        'row_color' => $this->setRowColors($booking->getPaymentStatus()),
                        'status_txt' => strtoupper($translator->trans($booking->getStatus())),
                        'date' => $booking->getDateEvent()->format('d/m/Y'),
                        'hour' => $booking->getTimeEvent()->format('H:i'),
                        'tour' => $booking->getAvailable()->getCategory()->getNamePt(),
                        'notes' => $booking->getNotes(),
                        'user_id' => $client->getId(),
                        'deposit' => $moneyFormatter->format($booking->getDepositAmount()),
                        'payment_status' => strtolower($booking->getPaymentStatus()),
                        'payment_log' => $booking->getStripePaymentLogs() ? 1 : 0,
                        'payment_status_txt' => strtoupper($translator->trans($booking->getPaymentStatus())),
                        'username' => $client->getUsername(),
                        'address' => $client->getAddress(),
                        'email' => $client->getEmail(),
                        'telephone' => $client->getTelephone(),
                        'total' => $moneyFormatter->format($booking->getAmount()),
                        'wp' => $client->getCvv() ? 1 : 0,
                        'posted_at' => $booking->getPostedAt()->format('d/m/Y H:i:s'),
                    ];
            }

            $counter = count($seeBookings);
            
            if ($counter > 0 && $counter <= 1500)
                return new JsonResponse([
                    'data' => $seeBookings, 
                    'options' => $counter, 
                    'pending' => $pending, 
                    'confirmed' => $confirmed, 
                    'canceled' => $canceled
                ]);
            
            else 
                return new JsonResponse([
                    'data' => '', 
                    'options' => $counter, 
                    'pending' => '', 
                    'confirmed' => '', 
                    'canceled' => ''
                ]);
        }
        else 
            return new JsonResponse([
                'data' => '', 
                'options' => 0, 
                'pending' => '', 
                'confirmed' => '', 
                'canceled' => ''
            ]);
        }

        return new JsonResponse([
            'data' => 'fields', 
            'options' => 0, 
            'pending' => '', 
            'confirmed' => '', 
            'canceled' => ''
        ]);
    }


    protected function getErrorMessages(\Symfony\Component\Form\Form $form) 
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors [] = $this->getErrorMessages($child);
            }
        }
        return $errors;
    }



    private function setRowColors($status){

    $green = ['completed', 'paid', 'paid', 'succeeded', 'approved'];

    $red = ['denied', 'failed', 'incomplete', 'canceled','refused','removed', 'uncaptured', 'canceled by user'];
    
    $yellow = ['held', 'placed', 'processing', 'pending', 'returned', 'cleared'];

    $blue = ['partial_refund', 'refunded', 'reversed', 'unclaimed'];

    if (in_array($status, $green))
        return 'w3-pale-green';

    else if (in_array($status, $blue))
        return 'w3-pale-blue';
    
    else if (in_array($status, $red))
        return 'w3-pale-red';
    
    else if (in_array($status, $yellow))

        return 'w3-pale-yellow';
    else
        return '';

    }

    public function bookingValidateUser(Request $request){
        $user = $this->getUser();
        $username = $request->request->get('username');
        $pass = $request->request->get('pass');
        $bookingId = $request->request->get('booking');
        $response = array();
        //check if mail is equal of current user
        if($user->getUsername() != $username){
            return new JsonResponse([
                'status' => 0,
                'message' => 'Utilizador inválido',
                'data' => ['info' => null]
            ]);
        }
        else if($user->getUsername() && password_verify($pass, $user->getPassword())){
            
            $em = $this->getDoctrine()->getManager();
            $booking = $em->getRepository(Booking::class)->find($bookingId);

            if(!$booking)
                return new JsonResponse([
                    'status' => 0,
                    'message' => 'Reserva não encontrada',
                    'data' => ['info' => null]
                ]);

            $client = $booking->getClient();

            return new JsonResponse([
                'status' => 1,
                'message' => 'Sucesso',
                'data' => [
                    'card_nr' => $client->getCardNr() === null ? '' : '<label>Nº Cartão Crédito: </label> '. $client->getCardNr(),
                    'cvv' => $client->getCvv() === null ? '': '<label>CVV: </label> '. $client->getCvv(),
                    'card_name' => $client->getCardName() === null ? '' : '<label>Titular Cartão: </label> '. $client->getCardName(),
                    'card_date' => $client->getCardDate() === null ? '' : '<label>Data Expiração: </label> ' .$client->getCardDate()
                ]
            ]);
        }

        return new JsonResponse([
            'status' => 0,
            'message' => 'Password inválida',
            'data' =>['info' => null]
        ]);
    }


    /*
    public function paymentStripe(Request $request, MoneyParser $moneyParser, MoneyFormatter $moneyFormatter){

        $em = $this->getDoctrine()->getManager();
        $company = $em->getRepository(Company::class)->find(1);
        $booking = $em->getRepository(Booking::class)->find($request->request->get('booking'));

        $stripe = new \Stripe\Stripe();
        $charge = new \Stripe\Charge();
        $customer = new \Stripe\Customer();

        $stripe->setApiKey($company->getStripeSk());

        $chargeClient = $request->request->get('chargeAmount') ? $request->request->get('chargeAmount') : 0 ;

        $amount = $moneyParser->parse($chargeClient);
        
        $request->request->get('stripeToken');

        if(!$booking)
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Reserva não encontrada!',
                'data' => 'STRP-#11')
            );

        if(!$company)
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Dados Empresa não encontrados!',
                'data' => 'STRP-#12')
            );

        if( $amount->getAmount() < 1 || !$amount )
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'Insira um valor maior que <b class="w3-text-red">0 (Zero) </b>!.<br> Altere e tente novamente.',
                'data' => 'STRP-#14')
            );


        if($amount->getAmount() > $booking->getAmount()->getAmount())
            return new JsonResponse(array(
                'status' => 0,
                'message' => 'O valor inserido: <b class="w3-text-red">'.$moneyFormatter->format($amount).'€</b> é maior do que o valor da reserva <b class="w3-text-green">'.$moneyFormatter->format($booking->getAmount()).'€</b>.<br> Altere e tente novamente.',
                'data' => 'STRP-#13')
            );


        //CREATE CARD PAYMENT
        try {
            $result = $charge->create(
                ['amount' => $amount->getAmount(), 
                'currency' => $company->getCurrency()->getCurrency(),
                'source' => $request->request->get('stripeToken'), // obtained with Stripe.js -> token
                'description' => $booking->getId().'-'.$booking->getAvailable()->getCategory()->getNameEn(),
                'metadata' => ['name' => $booking->getClient()->getCardName()]
            ]);

        //STRIPE PAYMENT STATUSES FROM API ARE : succeeded, pending, or failed.

        } catch(\Stripe\Error\Card $e) {

            $booking->getClient()->setCvv($booking->getClient()->getCvv().'# CARD DECLINED **');
            $em->persist($booking);
            $em->flush();  

            // Since it's a decline, \Stripe\Error\Card will be caught
            $response = array(
                'status' => 0,
                'order_status' => 'DECLINED',
                'message' => 'Credit Card declined, use other',
                'data' => 'STRP-#0');

            return new JsonResponse($response);

        } catch (\Stripe\Error\RateLimit $e) {
              // Too many requests made to the API too quickly
            $response = array(
                'status' => 0,
                'message' => 'Too many requests made to the API too quickly!',
                'data' => 'STRP-#1');
            return new JsonResponse($response);

        } 
        catch (\Stripe\Error\InvalidRequest $e) {

            // Invalid parameters were supplied to Stripe's API
            $response = array(
                'status' => 0,
                'message' => 'Invalid parameters were supplied to Stripe´s API',
                'data' => 'STRP-#2');
            return new JsonResponse($response);
        } 

        catch (\Stripe\Error\Authentication $e) {
              // Authentication with Stripe's API failed
              // (maybe you changed API keys recently)
            $response = array(
                'status' => 0,
                'message' => 'Authentication with Stripe´s API failed (API keys recently changed ?)',
                'data' => 'STRP-#3');
            return new JsonResponse($response);

        } catch (\Stripe\Error\ApiConnection $e) {
            
              // Network communication with Stripe failed
            $response = array(
                'status' => 0,
                'message' => 'Network communication with Stripe failed!',
                'data' => 'STRP-#4');
            return new JsonResponse($response);
        
        } catch (\Stripe\Error\Base $e) {
              // Display a very generic error to the user, and maybe send
              // yourself an email
             $response = array(
                'status' => 0,
                'message' => 'Ops! Something went wrong!',
                'data' => 'STRP-#5');
            return new JsonResponse($response);
        
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe

           return new JsonResponse([
                'status' => 0,
                'message' => 'Something else happened, completely unrelated to Stripe',
                'data' => 'STRP-#6'
            ]);      
        }

        //succeeded, pending, or failed.
        //completed == succeeded

        $status = $result->status == 'succeeded' ? 'completed' : $result->status;

        $booking->getClient()->setCvv($booking->getClient()->getCvv().'#'.$result->status);
        $em->persist($booking);
        $em->flush();                        
      
        return new JsonResponse([
            'status' => 1,
            'message' => 'Sucesso Cobrança Efetuada',
            'data' => $status
        ]);    
    }
*/


}
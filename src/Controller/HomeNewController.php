<?php


namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Translation\TranslatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\PessimisticLockException;
//LockMode::PESSIMISTIC_WRITE

use App\Entity\Staff;
use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Warning;
use App\Entity\Client;
use App\Entity\Gallery;
use App\Entity\User;
use App\Entity\Locales;
use App\Entity\AboutUs;
use App\Entity\Feedback;
use App\Entity\Available;
use App\Entity\TermsConditions;
use App\Entity\StripePaymentLogs;

use App\Service\MoneyFormatter;
use App\Service\RequestInfo;
use App\Service\FieldsValidator;
use App\Service\Stripe;
use App\Service\Menu;

use App\Service\EmailSender;

use App\Service\PdfGenerator;

use Money\Money;

class HomeNewController extends AbstractController
{

    private $expiration = 900;
    private $pdf_gen;
    private $session;
    private $emailer;

    
    public function __construct(SessionInterface $session, EmailSender $emailer, PdfGenerator $pdf_gen)
    {
        $this->emailer = $emailer;
        $this->pdf_gen = $pdf_gen;
        $this->session = $session;
    }
    
    public function index(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo, Menu $menu, TranslatorInterface $translator)
    {   
        //remove the session start_time
        $this->session->remove('start_time');

        $id = !$request->query->get('id') ? 'home': $request->query->get('id');

        $em = $this->getDoctrine()->getManager();
        
        //check the user brownser Locale
        $local = $reqInfo->getBrownserLocale($request);
        
        if(!$this->session->get('_locale')){
            $this->session->set('_locale', $local);
            return $this->redirectToRoute('index_new');
        }
        
        $today = new \DateTime('now');

        $whatsapp = $today->format('m') > 4 && $today->format('m') < 10  ? false : true;

        $cS = array();
        $comments = $em->getRepository(Feedback::class)->findBy(['visible' => true,'active' => true]);
        $locales = $em->getRepository(Locales::class)->findAll();
        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);
        $about = $em->getRepository(AboutUs::class)->findAll();
        $feedback = $em->getRepository(Feedback::class)->getCategoryScore();
        $category = $em->getRepository(Category::class)->findBy(['isActive' => 1],['orderBy' => 'ASC']);
        
        $staffs = $em->getRepository(Staff::class)->findBy(['is_active' => 1],['first_name' => 'ASC']);

        $categoryHl = $em->getRepository(Category::class)->findOneBy(['highlight' => 1, 'isActive' => 1],['orderBy' => 'ASC']);
        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'DESC']);

        $now = new \DateTime('tomorrow');

        $flag = false;
        $ord = [];

        if($categoryHl){
            if($categoryHl->getAvailable()){
                foreach ($categoryHl->getAvailable() as $available)
                    array_push($ord, $available->getDatetimeStart()->format('U'));
                sort($ord);
                
                for($t = 0; $t<count($ord); $t++) {
                    if($ord[$t] >= $now->format('U'))
                        $flag = true;
                }
            }    
        }
        
        $flag == true ? 
            $cH = [
                'adultAmount' => $moneyFormatter->format($categoryHl->getAdultPrice()),
                'childrenAmount'  => $moneyFormatter->format($categoryHl->getChildrenPrice()),
                'name' => $this->session->get('_locale') == 'pt_PT' ? $categoryHl->getNamePt() : $categoryHl->getNameEn(),
                'id' => $categoryHl->getId()]
            :
            $cH = [];
        
        foreach ($category as $categories){
            $flag = true;
            $ord = [];
            if($categories->getAvailable()){
                foreach ($categories->getAvailable() as $available)
                   array_push($ord, $available->getDatetimeStart()->format('U'));
                    
                sort($ord);
                
                for($t = 0; $t<count($ord); $t++) {
                    if($ord[$t] >= $now->format('U'))
                        $flag = false;
                }
            }
            
            $s = explode(":",$categories->getDuration());
            $minutes = (int)$s[0]*60 + (int)$s[1];

            $cS[]= [
                'adultAmount' => $moneyFormatter->format($categories->getAdultPrice()),
                'childrenAmount' => $moneyFormatter->format($categories->getChildrenPrice()),
                'name' => $this->session->get('_locale') == 'pt_PT' ?  $categories->getNamePt() : $categories->getNameEn(),
                'description' => $this->session->get('_locale') == 'pt_PT' ? $categories->getDescriptionPt() : $categories->getDescriptionEn(),
                'image' => $categories->getImage(),
                'id' => $categories->getId(),
                'warrantyPayment' => $categories->getwarrantyPayment(),
                'warrantyPaymentTxt' => $this->session->get('_locale') == 'pt_PT' ? $categories->getwarrantyPaymentPt() :  $categories->getwarrantyPaymentEn(),
                'duration' => $minutes,
                'no_stock' => $flag,
            ];
        }

        return $this->render('home/base.html', 
            [
                'warning' => $warning,
                'categories' => $cS,
                'category' => $cH,
                'galleries' => $gallery,
                'locales' => $locales, 
                'id' => '#'.$id,
                'company' => $company,
                'about' => $about,
                'host' => $reqInfo->getHost($request),
                'page' => 'index_new',
                'feedback' => $feedback,
                'comments' => $comments,
                'staffs' => $staffs,
                'menu' => $menu->site('index_new', $translator),
                'whatsapp' => $whatsapp
            ]);
    }


    public function info(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo, Menu $menu, TranslatorInterface $translator)
    {   
        $id = !$request->query->get('id') ? 'home': $request->query->get('id');
        
        $em = $this->getDoctrine()->getManager();
        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);
        
        $local = $request->getLocale();

        !$this->session->get('_locale') ? $this->session->set('_locale', $local) : false;

        $locales = $em->getRepository(Locales::class)->findAll();
        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'ASC']);
        return $this->render('info.html.twig', 
            [
                'colors'=> '',//$this->color(),
                'warning' => $warning,
                'locale' => null,
                'galleries' => $gallery,
                'locales' => $locales,
                'id' => '#'.$id,
                'company' => $company,
                'host' => $reqInfo->getHost($request),
                'page' => 'usefull-info-new',
                'menu' => $menu->site('usefull_info', $translator)
            ]
            );
    }

    public function validateBookingData(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo, FieldsValidator $fieldsValidator, TranslatorInterface $translator, Stripe $stripe){

        
        $err = [];
        $em = $this->getDoctrine()->getManager();
        $local = $request->getLocale();
        $locales = $em->getRepository(Locales::class)->findOneBy(['name' => $local]);

        if(!$locales)
             $locales = $em->getRepository(Locales::class)->findOneBy(['name' => 'pt_PT']);

        $locale = $locales->getName();
        if($this->getExpirationTime($request) == 1){ 
            $err[] = $translator->trans('session');
            return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 1
            ]);
        }

        //First part of validation 
        //the required fields on form, if empty send array to inform user what happen 
        $request->request->get('name') ? $name = $request->request->get('name') : $err[] = $translator->trans('part_seven.name');
        $request->request->get('email') ? $email = $request->request->get('email') : $err[] = 'Email *';
        $request->request->get('address') ? $address = $request->request->get('address') : $err[] = $translator->trans('part_seven.address');
        $request->request->get('telephone') ? $telephone = $request->request->get('telephone') : $err[] = $translator->trans('part_seven.telephone');
        $request->request->get('check_rgpd') && $request->request->get('check_rgpd') !== null  ? $rgpd = true : $err[] = $translator->trans('part_seven.rgpd');
        $request->request->get('event') ? $event = (int)$request->request->get('event') : $err[] = $translator->trans('event_not_found');
        
        !$request->request->get('adult') || $request->request->get('adult') <= 0 ? $err[] = $translator->trans('part_seven.adult') : $adult = $request->request->get('adult');
        $children = !$request->request->get('children') || $request->request->get('children') <= 0 ? 0 : $request->request->get('children');
        $baby = !$request->request->get('baby') || $request->request->get('baby') <= 0 ? 0 : $request->request->get('baby');

        if($err)
            return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 0
            ]);
        
        //Avoid user fake data
        $fieldsValidator->noFakeEmails($email) == 1 ? $err[] = $translator->trans('part_seven.email_invalid') : false;
        $fieldsValidator->noFakeTelephone($telephone) == 1 ? $err[] = $translator->trans('part_seven.telephone_invalid') : false;
        $fieldsValidator->noFakeName($name) == 1 ? $err[] = $translator->trans('part_seven.name_invalid') : false;
        //Check if the Event exits
        $available = $em->getRepository(Available::class)->find($event);

        if(!$available)
            $err[] = $translator->trans('event_not_found');

        if($err)
             return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 0
            ]);
        
        $company = $em->getRepository(Company::class)->find(1);

        //total amount of booking
        $amountA = Money::EUR(0);
        $amountC = Money::EUR(0);
        $total = Money::EUR(0);

        $amountA = $available->getCategory()->getAdultPrice();
        $amountA = $amountA->multiply($adult);
        $amountC = $available->getCategory()->getChildrenPrice();
        $amountC = $amountC->multiply($children);
        $total = $amountA->add($amountC);


        $deposit_percent = $available->getCategory()->getDeposit() != '0.00' ? 
            $available->getCategory()->getDeposit()
        :
            1;

        $temp_booking = [
            'booking' => [
                'tour' => $locales->getName() =='pt_PT' ? $available->getCategory()->getNamePt() : $available->getCategory()->getNameEn(),
                'date' => $available->getDatetimeStart()->format('d/m/Y'),
                'hour' => $available->getDatetimeStart()->format('H:i'),
                'total' => $total,
                'total_money' => $moneyFormatter->format($total),
                'total_to_charge' => $total_to_charge = $total->multiply($deposit_percent),
                'total_to_charge_money' => $moneyFormatter->format($total_to_charge),
                'to_be_charged' => $total->subtract(), 
                'charge_message' => $translator->trans('charge_message'),
                'to_be_charged_money' => $moneyFormatter->format($total),
                'to_be_charged' => $to_be_charged = $total->subtract($total_to_charge), 
                'to_be_charged_money' => $moneyFormatter->format($to_be_charged)
            
            ],
            'user' => [
                'name' => $name,
                'email' => $email,
                'address' => $address,
                'telephone' => $telephone
            ],
            'adult' =>[
                'quantity' => $adult,
                'subtotal' => $available->getCategory()->getAdultPrice(),
                'subtotal_money' => $moneyFormatter->format($available->getCategory()->getAdultPrice()),
                'total' => $amountA,
                'total_money' => $moneyFormatter->format($amountA)

            ],
            'children' => [
                'quantity' => $children,
                'subtotal_money' => $moneyFormatter->format($available->getCategory()->getChildrenPrice()),
                'subtotal' => $available->getCategory()->getChildrenPrice(),
                'total' => $amountC,
                'total_money' => $moneyFormatter->format($amountC)
            ],
            'baby' => [
                'quantity' => $baby,
                'subtotal' => Money::EUR(0),
                'subtotal_money' => $moneyFormatter->format(Money::EUR(0)),
                'total' => Money::EUR(0),
                'total_money' => $moneyFormatter->format(Money::EUR(0)),
            ],
            'payment' => [
                'enabled' => $available->getCategory()->getWarrantyPayment(),
                'public_key' => $company->getStripePK(),
                'payment_intent' => $available->getCategory()->getWarrantyPayment() ? $stripe->createUpdatePaymentIntent($company, $request, null) : null,
                'deposit' => ($available->getCategory()->getDeposit() != '0.00' ? $available->getCategory()->getDeposit() : 1 )*100
            ]
        ];

        return $this->render('home/modal_payment.html', 
            [
                'data' => $temp_booking
            ]);

    }

    public function setNewBooking(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo, FieldsValidator $fieldsValidator, TranslatorInterface $translator, Stripe $stripe){

        $err = [];

        if($this->getExpirationTime($request) == 1){ 
            $err[] = $translator->trans('session');
            return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 1
            ]);
        }


        $em = $this->getDoctrine()->getManager();
        $locales = $em->getRepository(Locales::class)->findOneBy(['name' => $request->getLocale()]);

        if(!$locales)
             $locales = $em->getRepository(Locales::class)->findOneBy(['name' => 'pt_PT']);

        $locale = $locales->getName();

        //First part of validation 
        //the required fields on form, if empty send array to inform user what happen 
        $request->request->get('name') ? $name = $request->request->get('name') : $err[] = $translator->trans('part_seven.name');
        $request->request->get('email') ? $email = $request->request->get('email') : $err[] = 'Email *';
        $request->request->get('address') ? $address = $request->request->get('address') : $err[] = $translator->trans('part_seven.address');
        $request->request->get('telephone') ? $telephone = $request->request->get('telephone') : $err[] = $translator->trans('part_seven.telephone');
        $request->request->get('check_rgpd') && $request->request->get('check_rgpd') !== null  ? $rgpd = true : $err[] = $translator->trans('part_seven.rgpd');
        $request->request->get('event') ? $event = (int)$request->request->get('event') : $err[] = $translator->trans('event_not_found');

        !$request->request->get('adult') || $request->request->get('adult') <= 0 ? $err[] = $translator->trans('part_seven.adult') : $adult = $request->request->get('adult');
        $children = !$request->request->get('children') || $request->request->get('children') <= 0 ? 0 : $request->request->get('children');
        $baby = !$request->request->get('baby') || $request->request->get('baby') <= 0 ? 0 : $request->request->get('baby');
        
        if($err)
            return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 0
            ]);
        
        //Avoid user fake data
        $fieldsValidator->noFakeEmails($email) == 1 ? $err[] = $translator->trans('part_seven.email_invalid') : false;
        $fieldsValidator->noFakeTelephone($telephone) == 1 ? $err[] = $translator->trans('part_seven.telephone_invalid') : false;
        $fieldsValidator->noFakeName($name) == 1 ? $err[] = $translator->trans('part_seven.name_invalid') : false;
        //Check if the Event exits
        $available = $em->getRepository(Available::class)->find($event);

        if(!$available)
            $err[] = $translator->trans('event_not_found');

        if($err)
             return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 0
            ]);


        //Create booking
        $em->getConnection()->beginTransaction();
            
        $available = $em->getRepository(Available::class)->find($event);
        $category = $em->getRepository(Category::class)->find($request->request->get('category'));
        $company = $em->getRepository(Company::class)->find(1);
        $tomorrow = new \DateTime('tomorrow');

        //Validate the Event with the Category and min Date, to prevent the user changing data on html injections (inspect elements)  
        $valid = $available->getCategory() == $category && $available->getDatetimeStart()->format('Y-m-d') >= $tomorrow->format('Y-m-d') ? true : false;


        if(!$valid)
            //throw new Exception("Error Processing Request Available", 1);
            return new JsonResponse([
                'status' => 4,
                'message' => $translator->trans('event_not_found'),
                'expiration' => 0
            ]);

        try {           
            $em->lock($available, LockMode::PESSIMISTIC_WRITE);
    
            //Get the total number of Pax.
            $paxCount = $adult + $children + $baby; 
            
            // When there is no availability for the number of Pax...
            if ($available->getStock() < $paxCount)
                // Abort and inform user.
                return new JsonResponse([
                    'status' => 4,
                    'message' => $translator->trans('part_seven.other_buy_it'),
                    'expiration' => 0
                ]);
           
            // Create Client
            $client = new Client();
            $client->setEmail($email);
            $client->setUsername($name);
            $client->setAddress($address);
            $client->setTelephone($telephone);
            $client->setRgpd($rgpd);
            $client->setLocale($locales);

            //total amount of booking
            $amountA = Money::EUR(0);
            $amountC = Money::EUR(0);
            $total = Money::EUR(0);

            $amountA = $available->getCategory()->getAdultPrice();
            $amountA = $amountA->multiply($adult);
            $amountC = $available->getCategory()->getChildrenPrice();
            $amountC = $amountC->multiply($children);
            $total = $amountA->add($amountC);   
            
            // Create Booking.
            $booking = new Booking();
            $booking->setAvailable($available);
            $booking->setAdult($adult);
            $booking->setChildren($children);
            $booking->setBaby($baby);
            $booking->setPostedAt(new \DateTime());
            $booking->setAmount($total);
            $booking->setClient($client);
            $booking->setDateEvent($available->getDatetimeStart());
            $booking->setTimeEvent($available->getDatetimeStart());
            $available->setStock($available->getStock() - $paxCount);

            $booking->setDepositAmount(Money::EUR(0));
            
            $em->persist($available);
            $em->persist($client);
            $em->persist($booking);
            
            $em->flush();

            if($available->getCategory()->getWarrantyPayment()){
            
                $i = $stripe->createUpdatePaymentIntent($company, $request, $booking);
                    
                if($i['status'] == 1){
                        
                    $payLogs = new StripePaymentLogs();
                    $payLogs->setLog(json_encode($i['data']));
                    $payLogs->setBooking($booking);
                    $em->persist($payLogs);

                    $em->flush();
                        
                    $booking->setPaymentStatus(Booking::STATUS_PROCESSING);
                    $booking->setStatus(Booking::STATUS_CANCELED);
                    $booking->setStripePaymentLogs($payLogs);
                    $em->persist($booking);
                    $em->flush();
                }
            }
                $em->getConnection()->commit();

        } 
        catch (\Exception $e) {
            //echo $e->getMessage();
            $em->getConnection()->rollBack();
            
            //throw $e;
            return new JsonResponse([
                'status' => 4,
                'message' => $translator->trans('opps_something_wrong').' '.$e->getMessage(),
                'data' => $e->getMessage(),
                'mail' => null,
                'expiration' => 0,
            ]);
        }

        if($available->getCategory()->getWarrantyPayment())
            return new JsonResponse([
            'status' => 99,
            'message' => 'waiting payment',
            'data' => $booking->getId(),
            'mail' => null,
            'expiration' => 0
        ]);


        $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $booking->getClient()->getLocale()]);
        /*Send email with pdf to client
        //$send = $this->sendBooking($company, $booking, $terms, $translator);
        $send = $this->sendEmail($booking, $request->getHost(), $translator, $terms);
            return new JsonResponse([
                'status' => 1,
                'message' => 'all valid',
                'data' => $booking->getId(),
                'mail' => 1, //$send,
                'expiration' => 0
            ]);
*/
        //Send email with pdf to client
        $send = $this->emailer->sendBookingTwo($company, $booking, $terms);
        //remove the session start_time
        $this->session->remove('start_time');

        return $send['status'] == 1 ?
            new JsonResponse([ 
                'status' => 1,
                'message' => 'all valid',
                'data' => $booking->getId(),
                'mail' => 1,
                'expiration' => 0])
            :
            new JsonResponse([
                'status' => 0,
                'message' => $send['status'],
                'data' => $send['status'].' #'.$booking->getId(),
                'mail' => 0,
                'expiration' => 0]);
    }

    public function onlineGetCharge(Request $request, Stripe $stripe)
    {
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $ch = $stripe->getPaymentCharge($company, $request);

        if($ch['status'] == 1){

            $b_id = explode('-', $ch['data']->data[0]->description);
            $deposit = Money::EUR($ch['data']->data[0]->amount);

            $booking = $em->getRepository(Booking::class)->find(str_replace('#','',$b_id[0]));
            
            $booking->getStripePaymentLogs()->setLog(json_encode($ch['data']->data[0]));
            $booking->setPaymentStatus($ch['data']->data[0]->status);
            $booking->setStatus(Booking::STATUS_PENDING);
            $booking->setDepositAmount($deposit);

            $em->persist($booking);
            $em->flush();

            $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $booking->getClient()->getLocale()]);

            $send = $this->emailer->sendBooking($company, $booking, $terms);

            return $send['status'] == 1 ?
                new JsonResponse([ 
                     'status' => 1,
                    'message' => $booking->getId(),
                    'data' => $ch])
                :
                new JsonResponse([
                    'status' => 1,
                    'message' => $booking->getId(),
                    'data' => $ch]);
        }
        else
            return new JsonResponse([
                'status' => 0,
                'message' => 'Unable to get Charge',
                'data' => null]);
    }

    public function userTranslation($lang, $page)
    {    
        $this->session->set('_locale', $lang);
        return $this->redirectToRoute($page);
    }





/*



    private function sendEmail(Booking $booking, $domain, TranslatorInterface $translator, TermsConditions $terms){

        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $pdf = $this->pdf_gen->voucher($company, $booking, $terms, 'S');

        $attachment = $pdf['status'] == 1
            ? 
            new \Swift_Attachment($pdf['pdf'], $translator->trans('booking').'#'.$booking->getId().'.pdf', 'application/pdf')
            : 
            false;

        $tour = $booking->getClient()->getLocale()->getName() == 'pt_PT' 
        ? 
            $booking->getAvailable()->getCategory()->getNamePt()
        :
            $booking->getAvailable()->getCategory()->getNameEn();


        $em = $this->getDoctrine()->getManager();

        $category = $booking->getAvailable()->getCategory();
                
        $client = $booking->getClient();
        
        $locale = $client->getLocale();
        
        $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $locale]);

        try {

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());       
        
        $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn();
        
        $mailer = new \Swift_Mailer($transport);
                    
        //$subject =  $translator->trans('booking').' #'.$booking->getId().' ('.$translator->trans('pending').')';
        $subject = $tour.' '.$translator->trans('booking').' #'.$booking->getId().' ('.$translator->trans('pending').')';
        
        $receipt_url = '';


            $text = $translator->trans('hello').' '.$booking->getClient()->getUsername().', \n'.
                $translator->trans('your_booking').' #'.$booking->getId().' - '. $tour.' '.
                $translator->trans('is').' '.$translator->trans('pending').', '.
                $translator->trans('soon_new_email_status').'\n'.
                $translator->trans('in_attach_info');


        if($booking->getStripePaymentLogs())
            if($booking->getStripePaymentLogs()->getLogObj())
                $receipt_url = $booking->getStripePaymentLogs()->getLogObj()->receipt_url;

        $message = (new \Swift_Message($subject))
            ->setFrom([$company->getEmail() => $company->getName()])
            ->setTo([$client->getEmail() => $client->getUsername(), $company->getEmail() => $company->getName()])
            ->addPart($text, 'text/plain')
            

  ->setBody(
                    $this->renderView(
                        'emails/booking.html',
                        [
                            'tour' => $tour,
                            'booking' => $booking,
                            'client' => $booking->getClient(),
                            'status' => 'pending',
                            'company' => $company,
                            'receipt_url' => $receipt_url
                        ]
                    ),
                'text/html');


            ->setBody(                
                $this->renderView(
                    'emails/booking-'.$locale ->getName().'.html.twig',
                    [


                        'id' => $booking->getId(),
                        'username' => $client->getUsername(),
                        'email' => $client->getEmail(),
                        'status' => $translator->trans('pending'),
                        'tour' => $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn(),
                        'date' => $booking->getAvailable()->getDatetimeStart()->format('d/m/Y'),
                        'hour' =>  $booking->getAvailable()->getDatetimeStart()->format('H:i'),
                        'adult' => $booking->getAdult(),
                        'children' => $booking->getChildren(),
                        'baby' => $booking->getBaby(),
                        'wp' => $category->getWarrantyPayment(),
                        'logo' => $company->getLinkMyDomain().'/upload/gallery/'.$company->getLogo(),
                        'terms' => !$terms ? '' : $terms->getName(),
                        'terms_txt' => !$terms ? '' : $terms->getTermsHtml(),
                        'company_name' => $company->getName(),
                        'receipt' => $translator->trans('receipt'),
                        'receipt_url' => $receipt_url
                    ]
                ),
                'text/html');
                

            //$message->getHeaders()->addTextHeader('List-Unsubscribe', $company->getLinkMyDomain());
            //$message->setReadReceiptTo($company->getEmail());
            //$message->setPriority(2);

        $attachment ? $message->attach($attachment) : false;
        
        $mailer->send($message);

        return ['status' => 1];

        }

        catch(Exception $e) {
            return ['status' => $e->getMessage()];  
        }
    }
*/

    //CHECK IF USER IS ON INTERVAL OF SUBMIT BOOKING ORDER 
    private function getExpirationTime($request) {

        $expired = 0;

        if(!$this->session->get('start_time'))
            $this->session->set('start_time', $request->server->get('REQUEST_TIME'));

        if($request->server->get('REQUEST_TIME') > $this->session->get('start_time') + $this->expiration)
            $expired = 1;

        return $expired;
    }
}


?>
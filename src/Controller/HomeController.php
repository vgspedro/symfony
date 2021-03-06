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
use App\Service\EmailSender;

use Money\Money;

class HomeController extends AbstractController
{
    /*set expiration on home page 15 minutes*/
    private $expiration = 900;

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }
    
    public function html(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo)
    {   
        //remove the session start_time
        $this->session->remove('start_time');

        $id = !$request->query->get('id') ? 'home': $request->query->get('id');

        $em = $this->getDoctrine()->getManager();
        
        //check the user brownser Locale
        $local = $reqInfo->getBrownserLocale($request);
        
        if(!$this->session->get('_locale')){
            $this->session->set('_locale', $local);
            return $this->redirectToRoute('index');
        }
        
        $cS = array();
        $comments = $em->getRepository(Feedback::class)->findBy(['visible' => true,'active' => true]);
        $locales = $em->getRepository(Locales::class)->findAll();
        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);
        $about = $em->getRepository(AboutUs::class)->findAll();
        $feedback = $em->getRepository(Feedback::class)->getCategoryScore();
        $category = $em->getRepository(Category::class)->findBy(['isActive' => 1],['orderBy' => 'ASC']);
        
        $categoryHl = $em->getRepository(Category::class)->findOneBy(['highlight' => 1],['orderBy' => 'ASC']);
        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'DESC']);

        $now = new \DateTime('tomorrow');

        $flag = false;
        $ord = [];

        if($categoryHl->getAvailable()){
            foreach ($categoryHl->getAvailable() as $available)
                array_push($ord, $available->getDatetimeStart()->format('U'));

            sort($ord);
                
            for($t = 0; $t<count($ord); $t++) {
                if($ord[$t] >= $now->format('U'))
                    $flag = true;
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

        return $this->render('base.html.twig', 
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
                'page' => 'index',
                'feedback' => $feedback,
                'comments' => $comments
            ]);
    }

    public function info(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo)
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
                'colors'=> $this->color(),
                'warning' => $warning,
                'locale' => null,
                'galleries' => $gallery,
                'locales' => $locales,
                'id' => '#'.$id,
                'company' => $company,
                'host' => $reqInfo->getHost($request),
                'page' => 'index_info'
            ]
            );
    }

    public function setBooking(Request $request, MoneyFormatter $moneyFormatter, RequestInfo $reqInfo, FieldsValidator $fieldsValidator, TranslatorInterface $translator, Stripe $stripe){

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
        
        //IF FIELDS IS NULL PUT IN ARRAY AND SEND BACK TO USER
        $request->request->get('name') ? $name = $request->request->get('name') : $err[] = $translator->trans('part_seven.name');
        $request->request->get('email') ? $email = $request->request->get('email') : $err[] = 'Email *';
        $request->request->get('address') ? $address = $request->request->get('address') : $err[] = $translator->trans('part_seven.address');
        $request->request->get('telephone') ? $telephone = $request->request->get('telephone') : $err[] = $translator->trans('part_seven.telephone');
        $request->request->get('check_rgpd') && $request->request->get('check_rgpd') !== null  ? $rgpd = true : $err[] = $translator->trans('part_seven.rgpd');
        $request->request->get('ev') ? $event = $request->request->get('ev') : $err[] = $translator->trans('part_seven.event');
        $request->request->get('adult') ? $adult = $request->request->get('adult') : $err[] = $translator->trans('part_seven.adult');
        $children = $request->request->get('children') ? $request->request->get('children') : 0;
        $baby = $request->request->get('baby') ? (int)$request->request->get('baby') : 0;

        $wp = $request->request->get('wp') == 'true' ? $request->request->get('wp') : false;
        
        //payment is required
        if($wp)
            $request->request->get('secret') ? $secret = $request->request->get('secret') : $err[] = $translator->trans('secret');
        if($err)

             return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 0
            ]);

        //NO FAKE DATA
        $fieldsValidator->noFakeEmails($email) == 1 ? $err[] = $translator->trans('part_seven.email_invalid') : false;
        $fieldsValidator->noFakeTelephone($telephone) == 1 ? $err[] = $translator->trans('part_seven.telephone_invalid') : false;
        $fieldsValidator->noFakeName($name) == 1 ? $err[] = $translator->trans('part_seven.name_invalid') : false;
        
        if($err)
             return new JsonResponse([
                'status' => 0,
                'message' => $err,
                'expiration' => 0
            ]);

        else{

            $em->getConnection()->beginTransaction();
            $available = $em->getRepository(Available::class)->find($event);

            if(!$available){
                //throw new Exception("Error Processing Request Available", 1);
               return new JsonResponse([
                    'status' => 4,
                    'message' => $translator->trans('event_not_found'),
                    'expiration' => 0
                ]);
            }

            try {           
                $em->lock($available, LockMode::PESSIMISTIC_WRITE);
    
                //Get the total number of Pax.
                $paxCount = $adult + $children + $baby; 

                // When there is no availability for the number of Pax...
                if ($available->getStock() < $paxCount) {
                    // Abort and inform user.
                    return new JsonResponse([
                        'status' => 4,
                        'message' => $translator->trans('part_seven.other_buy_it'),
                        'expiration' => 0
                    ]);
                }
           
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

                $em->persist($available);
                $em->persist($client);
                $em->persist($booking);
                $em->flush();

                if($wp){
                    $company = $em->getRepository(Company::class)->find(1);
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

 //               $em->flush();

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

            if($wp)
                return new JsonResponse([
                'status' => 99,
                'message' => 'waiting payment',
                'data' => $booking->getId(),
                'mail' => null,
                'expiration' => 0
            ]);
            
            //remove the session start_time
            $this->session->remove('start_time');
            $send = $this->sendEmail($booking, $request->getHost(), $translator);
            return new JsonResponse([
                'status' => 1,
                'message' => 'all valid',
                'data' => $booking->getId(),
                'mail' => $send,
                'expiration' => 0
            ]);
        }
    }


  /**
    *Get the receipt url to show on email
    *@param $request
    *@return json response of Request
    **/
    public function onlineGetCharge(Request $request, Stripe $stripe, TranslatorInterface $translator, EmailSender $emailer)
    {
        
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository(Company::class)->find(1);

        $ch = $stripe->getPaymentCharge($company, $request);

        if($ch['status'] == 1){

            $b_id = explode('-', $ch['data']->data[0]->description);
            $deposit = Money::EUR($ch['data']->data[0]->amount);

            $booking = $em->getRepository(Booking::class)->find(str_replace('#','',$b_id[0]));
            
            $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $booking->getClient()->getLocale()]);

            $booking->getStripePaymentLogs()->setLog(json_encode($ch['data']->data[0]));
            $booking->setPaymentStatus($ch['data']->data[0]->status);
            $booking->setStatus(Booking::STATUS_PENDING);
            $booking->setDepositAmount($deposit);

            $em->persist($booking);
            $em->flush();

            $send = $emailer->sendBooking($company, $booking, $terms);
            //$send = $this->sendEmail($booking, $request->getHost(), $translator);

            return new JsonResponse([
                'status' => 1,
                'message' => $booking->getId(),
                'data' => $ch
            ]);
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


    private function sendEmail(Booking $booking, $domain, TranslatorInterface $translator){

        $em = $this->getDoctrine()->getManager();

        $category = $booking->getAvailable()->getCategory();
        
        $company = $em->getRepository(Company::class)->find(1);
        
        $client = $booking->getClient();
        
        $locale = $client->getLocale();
        
        $terms = $em->getRepository(TermsConditions::class)->findOneBy(['locales' => $locale]);

        $transport = (new \Swift_SmtpTransport($company->getEmailSmtp(), $company->getEmailPort(), $company->getEmailCertificade()))
            ->setUsername($company->getEmail())
            ->setPassword($company->getEmailPass());       
        
        $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn();
        
        $mailer = new \Swift_Mailer($transport);
                    
        $subject =  $translator->trans('booking').' #'.$booking->getId().' ('.$translator->trans('pending').')';
        
        $receipt_url = '';

        if($booking->getStripePaymentLogs())
            if($booking->getStripePaymentLogs()->getLogObj())
                $receipt_url = $booking->getStripePaymentLogs()->getLogObj()->receipt_url;

        $message = (new \Swift_Message($subject))
            ->setFrom([$company->getEmail() => $company->getName()])
            ->setTo([$client->getEmail() => $client->getUsername(), $company->getEmail() => $company->getName()])
            ->addPart($subject, 'text/plain')
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
                        //'logo' => 'https://'.$domain.'/upload/gallery/'.$company->getLogo(),
                        'terms' => !$terms ? '' : $terms->getName(),
                        'terms_txt' => !$terms ? '' : $terms->getTermsHtml(),
                        'company_name' => $company->getName(),
                        'receipt' => $translator->trans('receipt'),
                        'receipt_url' => $receipt_url
                    ]
                ),
                'text/html'
            );
        
        $send = $mailer->send($message);
    }


    //CHECK IF USER IS ON INTERVAL OF SUBMIT BOOKING ORDER 
    private function getExpirationTime($request) {

        $expired = 0;

        if(!$this->session->get('start_time'))
            $this->session->set('start_time', $request->server->get('REQUEST_TIME'));

        if($request->server->get('REQUEST_TIME') > $this->session->get('start_time') + $this->expiration)
            $expired = 1;

        return $expired;
    }

    private function color(){
        return array(
        'w3-text-black',
        'w3-t-tour',
        'w3-text-cyan',
        'w3-text-indigo',
        'w3-text-green',
        'w3-text-light-blue',
        'w3-text-deep-purple',
        'w3-text-light-blue',
        'w3-text-teal',
        'w3-text-blue-gray',
        'w3-text-aqua',
        'w3-text-brown',
        'w3-text-amber',
        'w3-text-deep-orange',
        );
    }


}


?>
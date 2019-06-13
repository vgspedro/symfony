<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
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
use App\Entity\Available;
/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;
use App\Service\MoneyFormatter;
use Money\Money;
use App\Entity\TermsConditions;
/*https://packagist.org/packages/inacho/php-credit-card-validator*/
use Inacho\CreditCard;
class HomeController extends AbstractController
{
    /*set expiration on home page 15 minutes*/
    private $expiration = 900;

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }
    
    public function html(Request $request, MoneyFormatter $moneyFormatter)
    {   
        //remove the session start_time

        $this->session->remove('start_time');

        $id = !$request->query->get('id') ? 'home': $request->query->get('id');
        $em = $this->getDoctrine()->getManager();

        $locale = $this->getBrownserLocale($request);

        if ($this->session->get('_locale')){
            if($this->session->get('_locale')->getName())
                $locale = $this->session->get('_locale')->getName();

        }

        //$locale = $request->query->get('current-local') ? $request->query->get('current-local') : $this->getBrownserLocale($request);
        //$locale = $locale != 'pt_PT' ? 'en_EN' : 'pt_PT';

        $cS = array();
        $locales = $em->getRepository(Locales::class)->findAll();
        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);
        $about = $em->getRepository(AboutUs::class)->findAll();
        $category = $em->getRepository(Category::class)->findBy(['isActive' => 1],['orderBy' => 'ASC']);
        
        $categoryHl = $em->getRepository(Category::class)->findOneBy(['highlight' => 1],['orderBy' => 'ASC']);
        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'DESC']);

        $now = new \DateTime('tomorrow');

        $flag = false;
        $ord = array();

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
            $cH = array(
                'adultAmount' => $moneyFormatter->format($categoryHl->getAdultPrice()),
                'childrenAmount'  => $moneyFormatter->format($categoryHl->getChildrenPrice()),
                'namePt' => $categoryHl->getNamePt(),
                'nameEn' => $categoryHl->getNameEn(),
                'id' => $categoryHl->getId())
                :
                $cH = array();
        

        foreach ($category as $categories){
            $flag = true;
            $ord = array();
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
            
            $cS[]= array(
                'adultAmount' => $moneyFormatter->format($categories->getAdultPrice()),
                'childrenAmount' => $moneyFormatter->format($categories->getChildrenPrice()),
                'namePt' => $categories->getNamePt(),
                'nameEn' => $categories->getNameEn(),
                'descriptionPt' => $categories->getDescriptionPt(),
                'descriptionEn' => $categories->getDescriptionEn(),
                'image' => $categories->getImage(),
                'id' => $categories->getId(),
                'warrantyPayment' => $categories->getwarrantyPayment(),
                'warrantyPaymentPt' => $categories->getwarrantyPaymentPt(),
                'warrantyPaymentEn' => $categories->getwarrantyPaymentEn(),
                'duration' => $minutes,
                'no_stock' => $flag,
            );
        }
        return $this->render('base.html.twig', 
            array(
                'colors'=> $this->color(),
                'warning' => $warning,
                'categories' => $cS,
                'browser' => null,
                'category' => $cH,
                'galleries' => $gallery,
                'locale' => $locale,
                'locales' => $locales, 
                'id' => '#'.$id,
                'company' => $company,
                'about' => $about
                )
            );
    }

    public function info(Request $request, MoneyFormatter $moneyFormatter)
    {   
        $id = !$request->query->get('id') ? 'home': $request->query->get('id');
        
        $em = $this->getDoctrine()->getManager();
        $warning = $em->getRepository(Warning::class)->find(10);
        $company = $em->getRepository(Company::class)->find(1);

        //$locale = $request->query->get('current-local') ? $request->query->get('current-local') : $this->getBrownserLocale($request);
        //$locale = $locale != 'pt_PT' ? 'en_EN' : 'pt_PT';

        $locale = $this->getBrownserLocale($request);

        if ($this->session->get('_locale')){
            if($this->session->get('_locale')->getName())
                $locale = $this->session->get('_locale')->getName();

        }

        $locales = $em->getRepository(Locales::class)->findAll();
        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'ASC']);
        return $this->render('info.html.twig', 
            array(
                'colors'=> $this->color(),
                'warning' => $warning,
                'locale' => $locale,
                'galleries' => $gallery,
                'locales' => $locales,
                'id' => '#'.$id,
                'company' => $company
                )
            );
    }


    public function setBooking(Request $request, MoneyFormatter $moneyFormatter, \Swift_Mailer $mailer){

        $err = array();

        $em = $this->getDoctrine()->getManager();

        if ($this->session->get('_locale')){
            if($this->session->get('_locale')->getName())
                $locale = $this->session->get('_locale')->getName();
        }
        else
            $locale = $this->getBrownserLocale($request);

        if($this->getExpirationTime($request) == 1){ 
            $err[] = 'SESSION_END';
            $response = array(
                'status' => 3,
                'message' => 'session_end',
                'data' => $err,
                'mail' => null,
                'locale' => $locale,
                'expiration' => 1
            );
            return new JsonResponse($response);
        }
        
        //IF FIELDS IS NULL PUT IN ARRAY AND SEND BACK TO USER
        $request->request->get('name') ? $name = $request->request->get('name') : $err[] = 'NAME';
        $request->request->get('email') ? $email = $request->request->get('email') : $err[] = 'EMAIL';
        $request->request->get('address') ? $address = $request->request->get('address') : $err[] = 'ADDRESS';
        $request->request->get('telephone') ? $telephone = $request->request->get('telephone') : $err[] = 'TELEPHONE';
        $request->request->get('check_rgpd') && $request->request->get('check_rgpd') !== null  ? $rgpd = true : $err[] = 'RGPD';
        $request->request->get('evt') ? $userEvent = json_decode($request->request->get('evt')) : $err[] = 'event';
        $wp = $request->request->get('wp') == 'true' ? $request->request->get('wp') : false;
        
        if($wp){
            $request->request->get('name_card') ? $name_card = $request->request->get('name_card') : $err[] = 'CREDIT_CARD_NAME';
            $request->request->get('cvv') ? $cvv = $request->request->get('cvv') : $err[] = 'CVV';
            $request->request->get('date_card') ? $date_card = $request->request->get('date_card') : $err[] = 'CREDIT_CARD_DATE';
            $request->request->get('card_nr') ? $card_nr = $request->request->get('card_nr') : $err[] = 'CREDIT_CARD_NR';
        }

        if($err){
            $response = array(
                'status' => 0,
                'message' => 'fields empty',
                'data' => $err,
                'mail' => null,
                'locale' => $locale,
                'expiration' => 0
            );
            return new JsonResponse($response);
        }
        if($wp){
            $name != $name_card ? $err[] = 'NO_MATCH_NAMES' : false;
            $this->noFakeCcard($date_card,$cvv, $card_nr) ? $err[] = $this->noFakeCcard($date_card,$cvv, $card_nr) : false; 
        }
        //NO FAKE DATA
        $this->noFakeEmails($email) == 1 ? $err[] = 'EMAIL_INVALID' : false;
        $this->noFakeTelephone($telephone) == 1 ? $err[] = 'TELEPHONE_INVALID' : false;
        $this->noFakeName($name) == 1 ? $err[] = 'NAME_INVALID' : false;
        if($err){
            $response = array(
                'status' => 2,
                'message' => 'invalid fields',
                'data' => $err,
                'mail' => null,
                'locale' => $this->session->get('_locale')->getName(),
                'expiration' => 0
            );
            return new JsonResponse($response);
        }
        else{
        
        $locale = $this->session->get('_locale')->getName() ? $this->session->get('_locale')->getName() : 'pt_PT';
        $locales = $em->getRepository(Locales::class)->findOneBy(['name' => $locale]);
        
        if(!$locales)
            #throw new Exception("Error Processing Request Locales", 1);
            $err[] = 'OTHER_BUY_IT';
            $response = array(
                'status' => 0,
                'message' => 'no_vacancy_3',
                'data' => $err,
                'mail' => null,
                'locale' => $locale,
                'expiration' => 0
            );


        $em->getConnection()->beginTransaction();
        $available = $em->getRepository(Available::class)->find($userEvent->event);
          
        if(!$available){
        //    throw new Exception("Error Processing Request Available", 1);     
            $err[] = 'OTHER_BUY_IT';
            $response = array(
                'status' => 0,
                'message' => 'no_vacancy_1',
                'data' => $err,
                'mail' => null,
                'locale' => $locale,
                'expiration' => 0
            );
            return new JsonResponse($response);
        }

        try {           
            $em->lock($available, LockMode::PESSIMISTIC_WRITE);
    
             //Get the total number of Pax.
            $paxCount = $userEvent->adult + $userEvent->children + $userEvent->baby; 
        //total amount of booking
        $amountA = Money::EUR(0);
        $amountC = Money::EUR(0);
        $total = Money::EUR(0);
        $amountA = $available->getCategory()->getAdultPrice();
        $amountA = $amountA->multiply($userEvent->adult);
        $amountC = $available->getCategory()->getChildrenPrice();
        $amountC = $amountC->multiply($userEvent->children);
        $total = $amountA->add($amountC);   
            // When there is no availability for the number of Pax...
            if ($available->getStock() < $paxCount) {
                // Abort and inform user.
                $err[] = 'OTHER_BUY_IT';
                $response = array(
                    'status' => 0,
                    'message' => 'no_vacancy_1',
                    'data' => $err,
                    'mail' => null,
                    'locale' => $locale,
                    'expiration' => 0
                );
                return new JsonResponse($response);
            }
           
            // Create Client
            $client = new Client();
            $client->setEmail($email);
            $client->setUsername($name);
            $client->setAddress($address);
            $client->setTelephone($telephone);
            $client->setRgpd($rgpd);
            $client->setLocale($locales);
            //wp is set check if data from client isset
            if($available->getCategory()->getWarrantyPayment() && $wp){
                $client->setCardName($name_card);
                $client->setCvv($cvv);
                $client->setCardDate($date_card);
                $client->setCardNr($card_nr);
            }
            else if($available->getCategory()->getWarrantyPayment() && !$wp){
                $err[] = 'WP_SET_NO_CC_DATA';
                $response = array(
                    'status' => 0,
                    'message' => 'warranty payment set but no data to db',
                    'data' => $err,
                    'mail' => null,
                    'locale' => $locale,
                    'expiration' => 0
                );
                return new JsonResponse($response);
            }
            
            // Create Booking.
            $booking = new Booking();
            $booking->setAvailable($available);
            $booking->setAdult($userEvent->adult);
            $booking->setChildren($userEvent->children);
            $booking->setBaby($userEvent->baby);
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
            $em->getConnection()->commit();
            
        } catch (\Exception $e) {
            //echo $e->getMessage();
            $em->getConnection()->rollBack();
            
            //throw $e;
              $err[] = 'OTHER_BUY_IT';
                $response = array(
                    'status' => 0,
                    'message' => 'no_vacancy_2',
                    'data' => $err,
                    'mail' => null,
                    'locale' => $locale,
                    'expiration' => 0
                );
                return new JsonResponse($response);
        }
        $send = $this->sendEmail($mailer, $booking, $request->getHost());

        //remove the session start_time
        $this->session->remove('start_time');
        
        $response = array(
            'status' => 1,
            'message' => 'all valid',
            'data' =>  $booking->getId(),
            'mail' => $send,
            'locale' => $locales->getName(),
            'expiration' => 0
            );
        
        return new JsonResponse($response);
        }
    }



    private function sendEmail(\Swift_Mailer $mailer, Booking $booking, $domain){
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
                    
        $subject ='Reserva / Order #'.$booking->getId().' ('.$this->translateStatus('PENDING', $locale->getName()).')';
                    
        $message = (new \Swift_Message($subject))
            ->setFrom([$company->getEmail() => $company->getName()])
            ->setTo([$client->getEmail() => $client->getUsername(), $company->getEmail() => $company->getName()])
            ->addPart($subject, 'text/plain')
            ->setBody(
                $this->renderView(
                    'emails/booking-'.$locale ->getName().'.html.twig',
                    array(
                        'id' => $booking->getId(),
                        'username' => $client->getUsername(),
                        'email' => $client->getEmail(),
                        'status' => $this->translateStatus('PENDING', $locale->getName()),
                        'tour' => $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn(),
                        'date' => $booking->getAvailable()->getDatetimeStart()->format('d/m/Y'),
                        'hour' =>  $booking->getAvailable()->getDatetimeStart()->format('H:i'),
                        'adult' => $booking->getAdult(),
                        'children' => $booking->getChildren(),
                        'baby' => $booking->getBaby(),
                        'wp' => $category->getWarrantyPayment(),
                        'logo' => 'https://'.$domain.'/upload/gallery/'.$company->getLogo(),
                        'terms' => !$terms ? '' : $terms->getName(),
                        'terms_txt' => !$terms ? '' : $terms->getTermsHtml(),
                        'company_name' => $company->getName()
                    )
                ),
                'text/html'
            );
            $send = $mailer->send($message);
    }
    
    private function noFakeEmails($email) {
        $invalid = 0;        
        if($email){
            $validator = new \EmailValidator\Validator();
            $validator->isEmail($email) ? false : $invalid = 1;
            $validator->isSendable($email) ? false : $invalid = 1;
            $validator->hasMx($email) ? false : $invalid = 1;
            $validator->hasMx($email) != null ? false : $invalid = 1;
            $validator->isValid($email) ? false : $invalid = 1;
        }
        return $invalid;
    }


    private function noFakeCCard($date_card, $cvv, $card_nr) {
        $err = [];
        $card = CreditCard::validCreditCard($card_nr);
        if ($card['valid'] == 1) {
            $date = explode('/',$date_card);
            $validCvc = CreditCard::validCvc($cvv, $card['type']) == true ? false : $err[] = 'CVV_INVALID';
            $validDate = CreditCard::validDate($date[1], $date[0])  == true ? false : $err[] = 'DATE_CARD_INVALID';
        }
        
        else
            $err[] = 'CARD_NR_INVALID';
        return $err;
    }
    private function noFakeName($a){
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[^!@#\$%\^&\*\(\)\[\]:;]/", "", $a);
        return $invalid;
    }

    private function noFakeTelephone($a) {
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[0-9|\+?]{0,2}[0-9]{5,12}/", "", $a);
        return $invalid;
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

    private function translateStatus($status, $language){
        if ($language == 'pt_PT'){
            switch ($status) {
                case 'PENDING': 
                    $status = 'PENDENTE';
                break;
                case 'CANCELED': 
                    $status = 'CANCELADA';
                break;
                case 'CONFIRMED': 
                    $status = 'CONFIRMADA';
                break;
            
            }
        }
    
        return $status;
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

    private function getOS($request) { 
        $user_agent = $request->headers->get('user-agent');
        $os_platform    =   "Unknown OS Platform";
        $os_array       =   array(
                                '/windows nt 6.3/i'     =>  'Windows 8.1',
                                '/windows nt 6.2/i'     =>  'Windows 8',
                                '/windows nt 6.1/i'     =>  'Windows 7',
                                '/windows nt 6.0/i'     =>  'Windows Vista',
                                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                '/windows nt 5.1/i'     =>  'Windows XP',
                                '/windows xp/i'         =>  'Windows XP',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11',
                                '/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'        =>  'Mac OS 9',
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu',
                                '/iphone/i'             =>  'iPhone',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/android/i'            =>  'Android',
                                '/blackberry/i'         =>  'BlackBerry',
                                '/webos/i'              =>  'Mobile'
                            );
        foreach ($os_array as $regex => $value) { 
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        }   
        return $os_platform;
    }


    private function getBrowser($request) {
        $user_agent = $request->headers->get('user-agent');
        $browser        =   "Unknown Browser";
        
        $browser_array  =   array(
                                '/msie|trident/i'       =>  'Internet Explorer',
                                '/firefox/i'    =>  'Firefox',
                                '/safari/i'     =>  'Safari',
                                '/chrome/i'     =>  'Chrome',
                                '/opera/i'      =>  'Opera',
                                '/netscape/i'   =>  'Netscape',
                                '/maxthon/i'    =>  'Maxthon',
                                '/konqueror/i'  =>  'Konqueror',
                                '/mobile/i'     =>  'Handheld Browser'
                            );
        foreach ($browser_array as $regex => $value) { 
            if (preg_match($regex, $user_agent)) {
                $browser    =   $value;
            }
        }
        return $browser;
    }

    private function getPlatform($request) 
    { 
        $u_agent = $request->headers->get('user-agent');
        $platform = 'Unknown';
        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }
    
        return $platform; 
    }


    private function getVersion($request) 
    { 
        $u_agent = $request->headers->get('user-agent');
        $platform = 'Unknown';
        $pf ='';
        //First get the platform?
        if (preg_match('/Android/i', $u_agent))
            $pf = explode('Android ', $u_agent);
        elseif (preg_match('/Windows/i', $u_agent))
            $pf = explode('Windows ', $u_agent);
        if ($pf){
            $pf = explode(';', $pf[1]);
            $platform = $pf[0];     
        }
        
        return $platform; 

    }


    private function getBrownserLocale($request) 
    { 
        $u_agent = $request->headers->get('accept-language');
        $locale = 'pt_PT';

        if (!preg_match('/pt-PT/i', $u_agent))
            $locale="en_EN";
        
        return $locale; 

    }


}


?>
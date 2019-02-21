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
use App\Entity\Warning;
use App\Entity\Client;
use App\Entity\Gallery;
use App\Entity\User;
use App\Entity\Locales;
use App\Entity\Available;
/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;
use App\Service\MoneyFormatter;
use Money\Money;

class HomeController extends AbstractController
{

    /*set expiration on home page 15 minutes*/
    private $expiration = 9000;
    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session; 
    }


    public function html(Request $request, MoneyFormatter $moneyFormatter)
    {   
        $em = $this->getDoctrine()->getManager();

        $ua = $this->getBrowser();
        $locale = $ua['lang'];
    
        $cS = array();
        $locales = $em->getRepository(Locales::class)->findAll();
        $warning = $em->getRepository(Warning::class)->find(10);
        $category = $em->getRepository(Category::class)->findBy(['isActive' => 1],['namePt' => 'ASC']);
        $categoryHl = $em->getRepository(Category::class)->findOneBy(['highlight' => 1],['namePt' => 'ASC']);
        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'ASC']);

        $cH = array(
            'adultAmount' => $moneyFormatter->format($categoryHl->getAdultPrice()),
            'childrenAmount'  => $moneyFormatter->format($categoryHl->getChildrenPrice()),
            'namePt' => $categoryHl->getNamePt(),
            'nameEn' => $categoryHl->getNameEn(),
            'id' => $categoryHl->getId()
        );
        
        foreach ($category as $categories){
            
            $s = explode(":",$categories->getDuration());
            $minutes = (int)$s[0]*60 + (int)$s[1];
            
            $cS[]= array(
                'adultAmount' => $moneyFormatter->format($categories->getAdultPrice()),
                'childrenAmount'  => $moneyFormatter->format($categories->getChildrenPrice()),
                'namePt' => $categories->getNamePt(),
                'nameEn' => $categories->getNameEn(),
                'descriptionPt' => $categories->getDescriptionPt(),
                'descriptionEn' => $categories->getDescriptionEn(),
                'image' => $categories->getImage(),
                'id' => $categories->getId(),
                'warrantyPayment' => $categories->getwarrantyPayment(),
                'warrantyPaymentPt' => $categories->getwarrantyPaymentPt(),
                'warrantyPaymentEn' => $categories->getwarrantyPaymentEn(),
                'duration' => $minutes
            );
        }
        return $this->render('base.html.twig', 
            array(
                'colors'=> $this->color(),
                'warning' => $warning,
                'categories' => $cS,
                'browser' => $ua,
                'category' => $cH,
                'galleries' => $gallery,
                'locale' => $locale,
                'locales' => $locales)
            );
    }


    public function info(Request $request, MoneyFormatter $moneyFormatter)
    {   
        $em = $this->getDoctrine()->getManager();
        $warning = $em->getRepository(Warning::class)->find(10);
        $ua = $this->getBrowser();
        $locale = $ua['lang'];
        $locales = $em->getRepository(Locales::class)->findAll();
        $gallery = $em->getRepository(Gallery::class)->findBy(['isActive' => 1],['namePt' => 'ASC']);
        
        return $this->render('info.html.twig', 
            array(
                'colors'=> $this->color(),
                'warning' => $warning,
                'locale' => $locale,
                'galleries' => $gallery,
                'locales' => $locales)
            );
    }



    public function setBooking(Request $request, MoneyFormatter $moneyFormatter, \Swift_Mailer $mailer){
                    
        $err = array();
        $this->session->get('_locale')->getName();

        $em = $this->getDoctrine()->getManager();
        
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
                'locale' => $this->session->get('_locale')->getName()
            );
            return new JsonResponse($response);
        }

        if($wp){
            $name != $name_card ? $err[] = 'NO_MATCH_NAMES' : false;
            $this->noFakeCvv($cvv) ? $err[] = 'CVV_INVALID' : false;
            $this->noFakeCcard($card_nr) ? $err[] = 'CARD_NR_INVALID' : false;
            $this->noFakeCardDate($date_card) ? $err[] = 'DATE_CARD_INVALID' : false;
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
                'locale' => $this->session->get('_locale')->getName()
            );
            return new JsonResponse($response);
        }

        else{
        
        $locale = $this->session->get('_locale')->getName() ? $this->session->get('_locale')->getName() : 'pt_PT';

        $locales = $em->getRepository(Locales::class)->findOneBy(['name' => $locale]);
        
        if(!$locales)
            throw new Exception("Error Processing Request Locales", 1);

        $em->getConnection()->beginTransaction();

        $available = $em->getRepository(Available::class)->find($userEvent->event);
          if(!$available)
            throw new Exception("Error Processing Request Available", 1);
        
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
                    'locale' => $this->session->get('_locale')->getName()
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
                    'locale' => $this->session->get('_locale')->getName()
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
            
            throw $e;
              $err[] = 'OTHER_BUY_IT';
                $response = array(
                    'status' => 0,
                    'message' => 'no_vacancy_2',
                    'data' => $err,
                    'mail' => null,
                    'locale' => $this->session->get('_locale')->getName()
                );
                return new JsonResponse($response);
        }

        $send = $this->sendEmail($mailer, $booking);
        
        $response = array(
            'status' => 1,
            'message' => 'all valid',
            'data' =>  $booking->getId(),
            'mail' => $send,
            'locale' => $locales->getName());
        
        return new JsonResponse($response);
        }
    }

    private function sendEmail(\Swift_Mailer $mailer, Booking $booking){

        $em = $this->getDoctrine()->getManager();

        $category = $booking->getAvailable()->getCategory();

        $client = $booking->getClient();

        $locale = $client->getLocale();

        $transport = (new \Swift_SmtpTransport($_ENV['EMAIL_SMTP'], $_ENV['EMAIL_PORT'], $_ENV['EMAIL_CERTIFICADE']))
            ->setUsername($_ENV['EMAIL'])
            ->setPassword($_ENV['EMAIL_PASS']);       

        $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn();

        $mailer = new \Swift_Mailer($transport);
                    
        $subject ='Reserva / Order #'.$booking->getId().' ('.$this->translateStatus('PENDING', $locale->getName()).')';
                    
        $message = (new \Swift_Message($subject))
            ->setFrom([$_ENV['EMAIL'] => $_ENV['EMAIL_USERNAME']])
            ->setTo([$client->getEmail() => $client->getUsername(), $_ENV['EMAIL'] => $_ENV['EMAIL_USERNAME'] ])
            ->addPart($subject, 'text/plain')
            ->setBody(
                $this->renderView(
                    'emails/booking-'.$locale ->getName().'.html.twig',
                    array(
                        'id' => $booking->getId(),
                        'username' => $client->getUsername(),
                        'email' => $client->getEmail(),
                        'status' => $this->translateStatus('PENDING', $locale ->getName()),
                        'tour' => $locale->getName() == 'pt_PT' ? $category->getNamePt() : $category->getNameEn(),
                        'date' => $booking->getAvailable()->getDatetimeStart()->format('d/m/Y'),
                        'hour' =>  $booking->getAvailable()->getDatetimeStart()->format('H:i'),
                        'adult' => $booking->getAdult(),
                        'children' => $booking->getChildren(),
                        'baby' => $booking->getBaby(),
                        'wp' => $category->getWarrantyPayment(),
                        'logo' => 'https://tarugatoursbenagilcaves.pt/images/logo.png'
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

    private function noFakeName($a){
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[^!@#\$%\^&\*\(\)\[\]:;]/", "", $a);
        return $invalid;
    }

    private function noFakeCardDate($a){
        $invalid = 0;   
        if($a){
            $now = new \DateTime('now');
            $date = explode('/',$a);

            $invalid = $date[0] <= $now->format('m') || $date[1] < $now->format('Y') ? 1 : 0;
        }
        return $invalid;
    }


    private function noFakeCvv($a){
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[0-9]{3}/", "", $a);
        return $invalid;
    }

    private function noFakeCcard($a){
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[0-9]{16}/", "", $a);
        return $invalid;
    }

    private function noFakeTelephone($a) {
        $invalid = 0;        
        if($a)
            $invalid = preg_replace("/[0-9|\+?]{0,2}[0-9]{5,12}/", "", $a);
        return $invalid;
    }

    private function getExpirationTime() {
        return $this->expiration;
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




    private function getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        $os_platform  = "Unknown OS Platform";

         $os_array     = array(
                          '/windows nt 10/i'      =>  'Windows 10',
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

        foreach ($os_array as $regex => $value)
         if (preg_match($regex, $u_agent))
                $os_platform = $value;


        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } 
        
        $lang = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $current_country = '';
        $current_city = '';


        // if the link is down http://api.hostip.info/get_html.php bullshit happens, how to solve it.....
        
        $html = '';

        if ($html)
        {
            file_get_contents('http://api.hostip.info/get_html.php?ip='.$_SERVER['REMOTE_ADDR']);
            $country = explode('Country: ',$country);
            $city = explode('City: ',$country[1]);
            $ip = explode('IP: ',$city[1]);
            $location = explode('City: ',$country[1]);
            $current_city = $ip[0];
            $current_country = $location[0];
        
        $response =  array(
            'name'      => $bname,
            'os'        => ucfirst($platform),
            'platform'  => ucfirst($os_platform),
            'lang'      => $lang[0],
            'country'   => $current_country,
            'city'      => $current_city,
            'ip'        => $_SERVER['REMOTE_ADDR']
        );
        }


        else $response =  array(
            'name'      => $bname,
            'os'        => ucfirst($platform),
            'platform'  => ucfirst($os_platform),
            'lang'      => $lang[0],
            'country'   => '-/-',
            'city'      => '-/-',
            'ip'        => $_SERVER['REMOTE_ADDR']
        );



        return $response;
    } 
}

?>
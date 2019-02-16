<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Warning;
use App\Entity\Blockdates;
use App\Entity\Client;
use App\Entity\Gallery;
use App\Form\BookingType;
use App\Entity\User;
use App\Entity\Locales;
/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;
use App\Service\MoneyFormatter;

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


 public function setBooking(Request $request, MoneyFormatter $moneyFormatter){
                    
        $err = array();
        $this->session->get('_locale')->getName();
        
        //IF FIELDS IS NULL PUT IN ARRAY AND SEND BACK TO USER
        $request->request->get('name') ? $name = $request->request->get('name') : $err[] = 'NAME';
        $request->request->get('email') ? $email = $request->request->get('email') : $err[] = 'EMAIL';
        $request->request->get('address') ? $address = $request->request->get('address') : $err[] = 'ADDRESS';
        $request->request->get('telephone') ? $telephone = $request->request->get('telephone') : $err[] = 'TELEPHONE';
        $request->request->get('check_rgpd') && $request->request->get('check_rgpd') !== null  ? $rgpd = $request->request->get('rgpd') : $err[] = 'RGPD';
        $request->request->get('evt') ? $userEvent = json_decode($request->request->get('evt')) : $err[] = 'event';
        $wp = $request->request->get('wp') == 'true' ? $request->request->get('wp') : false;
        
        if($wp){
            $request->request->get('name_card') ? $name_card = $request->request->get('name_card') : $err[] = 'CREDIT_CARD_NAME';
            $request->request->get('cvv') ? $cvv = $request->request->get('cvv') : $err[] = 'CVV';
            $request->request->get('date_card') ? $date_card = $request->request->get('date_card') : $err[] = 'CREDIT_CARD_DATE';
            $request->request->get('card_nr') ? $card_nr = $request->request->get('card_nr') : $err[] = 'CREDIT_CARD_NR';
    
        }

/*
event id
        $userEvent->event
        $userEvent->adult
        $userEvent->children
        $userEvent->baby
*/        

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
         
/*

        $language = $request->request->get('language');

                    $booking = new Booking();

                    $booking->setAdult($newBook['adult']);
                    $booking->setChildren($newBook['children']);
                    $booking->setBaby($newBook['baby']);
                    $booking->setDate($date->format('Y-m-d'));
                    $booking->setHour($hour->format('H:i'));
                    $booking->setMessage($newBook['message']);
                    $booking->setTourType($newBook['tourtype']);
                    $booking->setPostedAt(new \DateTime());

                    $client = new Client();

                    $client->setBooking($booking);
                    $client->setEmail($newBook['email']);
                    $client->setUsername($newBook['name']);
                    $client->setAddress($newBook['address']);
                    $client->setTelephone($newBook['telephone']);
                    $client->setRgpd($newBook['rgpd']);
                    $client->setLanguage($language);
                    $em->persist($client);
                    $em->persist($booking);
                    $em->flush();
*/
                    $response = array(
                        'status' => 1,
                        'message' => 'all valid',
                        'data' =>  'no err',
                        'mail' => null,
                        'locale' => $this->session->get('_locale')->getName()
                    );
        
                return new JsonResponse($response);
    }




    private function sendEmail(\Swift_Mailer $mailer, Booking $booking){

        $category = $em->getRepository(Category::class)->find($booking->getTourType());

        $tour = $language =='en' ? $category->getNameEn() : $category->getNamePt();

        $date = date_create_from_format("Y-m-d", $booking->getDate());

        $transport = (new \Swift_SmtpTransport('smtp.sapo.pt', 465, 'ssl'))
            ->setUsername('vgspedro15@sapo.pt')
            ->setPassword('ledcpu');

        $mailer = new \Swift_Mailer($transport);
                    
        $subject ='Reserva / Order #'.$booking->getId().' ('.$this->translateStatus('PENDING', $language).')';
                    
        $message = (new \Swift_Message($subject))
            ->setFrom(['vgspedro@gmail.com' => 'Pedro Viegas'])
            ->setTo([$client->getEmail() => $client->getUsername(), 'vgspedro15@sapo.pt' => 'Pedro Viegas'])
            ->addPart($subject, 'text/plain')
            ->setBody(
                $this->renderView(
                    'emails/booking-'.$language.'.html.twig',
                    array(
                        'id' => $booking->getId(),
                        'username' => $client->getUsername(),
                        'email' => $client->getEmail(),
                        'status' => $this->translateStatus('PENDING', $language),
                        'tour' => $tour,
                        'date' => $date->format('d/m/Y'),
                        'hour' => $booking->getHour(),
                        'adult' => $booking->getAdult(),
                        'children' => $booking->getChildren(),
                        'baby' => $booking->getBaby(),
                        'message' => $booking->getMessage(),
                        'logo' => 'https://tarugatoursbenagilcaves.pt/images/logo.png'
                    )
                ),
                'text/html'
            );
            $send = $mailer->send($message);

                    $response = array(
                        'result' => 1,
                        'message' => 'success',
                        'data' => $booking->getId(),
                        'mail' => $send,
                        'session' => 1
                    );

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
        if ($language == 'pt-pt'){
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
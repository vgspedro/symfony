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
/*https://github.com/nojacko/email-validator*/
use EmailValidator\EmailValidator;
use App\Service\MoneyFormatter;

class HomeController extends AbstractController
{

    /*set expiration on home page 15 minutes*/
    private $expiration = 900;

    public function html(Request $request, ValidatorInterface $validator, \Swift_Mailer $mailer, SessionInterface $session, MoneyFormatter $moneyFormatter)
    {
        $ua = $this->getBrowser();

        $validate = new Booking();
        
        $time = new \DateTime('now');

        if (!$request->isXmlHttpRequest()) {
            $session->clear();
            $session->set('expired', $time->getTimestamp());
        }

        $locale = $ua['lang'];

        $form = $this->createForm(BookingType::class, $validate);        
        
        //clients dont need this so we remove it 
        $form->remove('notes'); 
        
        $sessionEnd = ($time->getTimestamp() - $session->get('expired')) < $this->getExpirationTime() ? true : false; 

        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {
          
            $form->submit($request->request->get($form->getName()));
            
            if($form->isSubmitted() && $sessionEnd == true){
                
                if($form->isValid()){ 
                    $err = array();
                    $newBook = $request->request->get($form->getName());
                    //set email language

                    //CHECK IF EMAIL IF VALID
                    if($newBook['email']){
                        $validator = new \EmailValidator\Validator();
                        $validator->isEmail($newBook['email']) ? false : $err[] = 'EMAIL_INVALID';
                        $validator->isSendable($newBook['email']) ? false : $err[] = 'EMAIL_INVALID';
                        $validator->hasMx($newBook['email']) ? false : $err[] = 'EMAIL_INVALID';
                        $validator->hasMx($newBook['email']) != null ? false : $err[] = 'EMAIL_INVALID';
                        $validator->isValid($newBook['email']) ? false : $err[] = 'EMAIL_INVALID';
                    }

                    if($err){
                        $response = array(
                        'result' => 0,
                        'message' => 'mail_invalid',
                        'data' => $err,
                        'mail' =>'',
                        'session' => 1
                        );
                        return new JsonResponse($response);
                    }
                    
                    $language = $request->request->get('language');

                    $date = date_create_from_format("d/m/Y", $newBook['date']);
                    $hour = date_create_from_format("H:i", $newBook['hour']);

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
                        ->setTo([$client->getEmail() => $client->getUsername(),
                            'vgspedro15@sapo.pt' => 'Pedro Viegas'])
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
                else{   
                    $response = array(
                        'result' => 0,
                        'message' => 'fail',
                        'data' => $this->getErrorMessages($form),
                        'mail' =>'',
                        'session' => 1
                    );
                }
            }
            else
                $response = array(
                    'result' => 2,
                    'message' => 'expired',
                    'data' => '',
                    'mail' =>'',
                    'session' => $sessionEnd
                );
                return new JsonResponse($response);
        }
        else{

            $cS = array();
            
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
            foreach ($category as $categories)
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
                    'warrantyPaymentEn' => $categories->getwarrantyPaymentEn()
                    );
          

            return $this->render('base.html.twig', array(
                'form' => $form->createView(),
                'colors'=> $this->color(),
                'warning' => $warning,
                'categories' => $cS,
                'browser' => $ua,
                'category' => $cH,
                'galleries' => $gallery,
                'locale' => $locale
            ));
        }
    }

    private function getExpirationTime() {
        return $this->expiration;
    }

    protected function getErrorMessages(\Symfony\Component\Form\Form $form) 
    {
        $errors = array();
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
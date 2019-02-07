<?php
namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Category;
use App\Entity\Blockdates;
use App\Entity\Booking;
use App\Entity\Event;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\EasyText;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\CategoryType;
use App\Form\GalleryType;
use App\Form\BlockdatesType;
use App\Form\EventType;
use App\Form\EasyTextType;
use App\Service\MoneyFormatter;

class AvailableController extends AbstractController
{

    public function adminAvailable(Request $request)
    {
        return $this->render('admin/available.html');
    }

    public function adminAvailableSearch(Request $request, MoneyFormatter $moneyFormatter)
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

        $booking = $this->getDoctrine()->getManager()->getRepository(Booking::class)->bookingFilter($start, $end);

        if ($booking){

            foreach ($booking as $bookings) {
            
                if ($bookings->getStatus() ==='canceled')
                    $canceled = $canceled+1;
                else if ($bookings->getStatus() ==='pending')
                    $pending = $pending+1;
                else if ($bookings->getStatus() ==='confirmed')
                    $confirmed = $confirmed+1;

                $date = date_create_from_format("Y-m-d", $bookings->getDate());

                //$category = $em->getRepository(Category::class)->find($bookings->getTourType());
                
                $client = $bookings->getClient();



                $seeBookings[] =
                    array(
                    'booking' => $bookings->getId(),
                    'adult' => $bookings->getAdult(),
                    'children' => $bookings->getChildren(),
                    'baby' => $bookings->getBaby(),
                    'status' => $bookings->getStatus(),
                    'date' => $date->format('d/m/Y'),
                    'hour' => $bookings->getHour(),
                    'tour' => $bookings->getTourType()->getNamePt(),
                    'notes' => $bookings->getNotes(),
                    'user_id' => $client->getId(),   
                    'username' => $client->getUsername(),
                    'address' => $client->getAddress(),
                    'email' => $client->getEmail(),          
                    'telephone' => $client->getTelephone(),
                    'total' => $moneyFormatter->format($bookings->getTotalBookingAmount()).'€'
                    );          
            }


            $counter = count($seeBookings);
            
            if ($counter > 0 && $counter <= 1500)
            
                $response = array(
                    'data' => $seeBookings, 
                    'options' => $counter, 
                    'pending' => $pending, 
                    'confirmed' => $confirmed, 
                    'canceled' => $canceled);
            else 
                $response = array(
                    'data' => '', 
                    'options' => $counter, 
                    'pending' => '', 
                    'confirmed' => '', 
                    'canceled' => '');

        }
        else 
            $response = array(
                'data' => '', 
                'options' => 0, 
                'pending' => '', 
                'confirmed' => '', 
                'canceled' => '');
        }
        else 
            $response = array(
                'data' => 'fields', 
                'options' => 0, 
                'pending' => '', 
                'confirmed' => '', 
                'canceled' => '');

        return new JsonResponse($response);
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
                case 'pending': 
                    $status = 'PENDENTE';
                break;
                case 'canceled': 
                    $status = 'CANCELADA';
                break;
                case 'confirmed': 
                    $status = 'CONFIRMADA';
                break;
            
            }
        }
    

    return $status;
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
<?php
namespace App\Service;


class RequestInfo
{


    public function getBrownserLocale($request) 
    { 
        $u_agent = $request->headers->get('accept-language');
        $locale = 'pt_PT';

        if (!preg_match('/pt-/i', $u_agent))
            $locale = "en_EN";
        return $locale; 

    }

    public function getOS($request) { 
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

    public function getBrowser($request) {
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


    public function getPlatform($request) 
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

    public function getVersion($request) 
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

    public function getHost($request) 
    { 
        $domain = $request->headers->get('host');
        return preg_match('/127/i', $domain) || preg_match('/192/i', $domain) || preg_match('/demo/i', $domain) ? true : false;
    }





    public function browserInfo($request){    
        $r = array(
            'name'      => $this->getBrowser($request),
            'os'        => $this->getOs($request),
            'platform'  => $this->getPlatform($request),
            'version'   => $this->getVersion($request),
            'lang'      => $request->getLocale(),
            'ip'        => $request->getClientIp(),
            'country'   => '-/-',
            'city'      => '-/-',
            'uri'       => $request->getUri(),
            'browser_locale' => $this->getBrownserLocale($request)
        );
    return $r;
    }

  


}
